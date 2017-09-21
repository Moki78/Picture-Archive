<?php

namespace PictureArchiveBundle\Service;

use Doctrine\ORM\EntityManager;
use PictureArchiveBundle\Component\Configuration;
use PictureArchiveBundle\Component\FileInfo;
use PictureArchiveBundle\Entity\MediaFile;
use PictureArchiveBundle\Event\ImportEvent;
use PictureArchiveBundle\Service\Import\Analyser;
use PictureArchiveBundle\Service\Import\FileRunner;
use PictureArchiveBundle\Service\Index\FileProcessor;
use PictureArchiveBundle\Util\ImageExif;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 *
 * @package PictureArchiveBundle\Import
 * @author Moki <picture-archive@mokis-welt.de>
 */
class Import
{
    /**
     * @var FileRunner
     */
    private $fileRunner;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var FileProcessor
     */
    private $fileProcessor;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Analyser
     */
    private $analyser;

    /**
     * @var ImageExif
     */
    private $imageExifService;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * Processor constructor.
     * @param EntityManager $em
     * @param EventDispatcherInterface $eventDispatcher
     * @param FileRunner $fileRunner
     * @param FileProcessor $fileProcessor
     * @param Analyser $analyser
     * @param Configuration $configuration
     * @param ImageExif $imageExif
     * @internal param Logger $logger
     */
    public function __construct(
        EntityManager $em,
        EventDispatcherInterface $eventDispatcher,
        FileRunner $fileRunner,
        FileProcessor $fileProcessor,
        Analyser $analyser,
        Configuration $configuration,
        ImageExif $imageExif
    )
    {
        $this->em = $em;
        $this->fileRunner = $fileRunner;
        $this->fileProcessor = $fileProcessor;
        $this->configuration = $configuration;
        $this->analyser = $analyser;
        $this->imageExifService = $imageExif;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param ProgressBar $progressBar
     * @return bool
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function run(ProgressBar $progressBar): bool
    {
        $importEvent = new ImportEvent();

        $this->eventDispatcher->dispatch(
            'picture-archive.import.start',
            $importEvent->setStatus(ImportEvent::STATUS_START)
        );

        $this->fileProcessor->initialize();

        $this->fileRunner->loadFiles();

        $progressBar->start($this->fileRunner->count());

        foreach ($this->fileRunner->getFileCollection() as $importFile) {
            $progressBar->advance();

            $importEvent = new ImportEvent();
            $importEvent->setFileInfo($importFile);

            try {
                $this->setMediaDate($importFile);

                $importFile = $this->analyser->analyse($importFile);
                if (FileInfo::STATUS_INVALID === $importFile->getStatus()) {
                    $importEvent->setStatus(ImportEvent::STATUS_ANALYSE_FAILED);
                    $importEvent->setMessage($importFile->getStatusMessage());

                    $this->eventDispatcher->dispatch('picture-archive.import.analyse.failed', $importEvent);
                    continue;
                }

                $mediaFile = $this->createEntity($importFile);

                $status = $this->fileProcessor->saveFile($mediaFile, $importFile->getPathname());

                if (!$status) {
                    // could not rename file
                    $importEvent->setStatus(ImportEvent::STATUS_SAVE_FAILED);
                    $importEvent->setMessage('rename failed');

                    $this->eventDispatcher->dispatch('picture-archive.import.save.failed', $importEvent);

                    continue;
                }

                $mediaFile->setStatus(MediaFile::STATUS_IMPORTED);

                // save file
                $this->em->persist($mediaFile);
                $this->em->flush();

                $importEvent->setStatus(ImportEvent::STATUS_SUCCESS);

                $this->eventDispatcher->dispatch(
                    'picture-archive.import.success',
                    $importEvent->setStatus(ImportEvent::STATUS_SUCCESS)
                );

            } catch (\Exception $e) {
                // log Exception
                $importEvent->setStatus(ImportEvent::STATUS_ERROR);
                $importEvent->setMessage($e->getMessage());

                $this->eventDispatcher->dispatch('picture-archive.import.error', new ImportEvent(1, ''));


                continue;
            }
        }
        $progressBar->finish();

        $this->eventDispatcher->dispatch(
            'picture-archive.import.finish',
            $importEvent->setStatus(ImportEvent::STATUS_FINISH)
        );

        return true;
    }

    /**
     * @param FileInfo $importFile
     * @return FileInfo
     */
    public function setMediaDate(FileInfo $importFile): FileInfo
    {
        $createDate = $this->imageExifService->getCreationDate($importFile->getPathname());
        if ($createDate) {
            $importFile->setMediaDate($createDate);
        }

        return $importFile;
    }

    /**
     * @param FileInfo $importFile
     * @return MediaFile
     */
    private function createEntity(FileInfo $importFile): MediaFile
    {
        $entity = new MediaFile();
        $entity
            ->setType(MediaFile::TYPE_UNKNOWN)
            ->setStatus(MediaFile::STATUS_NEW)
            ->setMimeType($importFile->getMimeType())
            ->setHash($importFile->getFileHash())
            ->setMediaDate($importFile->getMediaDate())
            ->setPath($this->generateFilename($importFile))
            ->setName($importFile->getFilename());

        return $entity;
    }

    /**
     * @param FileInfo $importFile
     * @return string
     */
    private function generateFilename(FileInfo $importFile): string
    {
        $subDirectories = $this->configuration->getArchiveFiletypeSubdirectory();

        if (array_key_exists($importFile->getFileType(), $subDirectories)) {
            $filepath = sprintf(
                '%s/%s/%s',
                $subDirectories[$importFile->getFileType()],
                $importFile->getFileDate()->format('Y/m'),
                $importFile->getFilename()
            );
        } else {
            $filepath = sprintf(
                '%s/%s',
                $importFile->getFileDate()->format('Y/m'),
                $importFile->getFilename()
            );
        }

        return $filepath;
    }
}
