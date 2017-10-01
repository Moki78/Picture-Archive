<?php

namespace PictureArchiveBundle\Service;

use Doctrine\ORM\EntityManager;
use PictureArchiveBundle\Component\FileInfo;
use PictureArchiveBundle\Component\FilepathGenerator;
use PictureArchiveBundle\Entity\MediaFile;
use PictureArchiveBundle\Event\ImportEvent;
use PictureArchiveBundle\Service\Import\Analyser;
use PictureArchiveBundle\Service\Import\FileProcessor;
use PictureArchiveBundle\Service\Import\FileRunner;
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
     * @var Analyser
     */
    private $analyser;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;
    /**
     * @var FilepathGenerator
     */
    private $filepathGenerator;

    /**
     * Processor constructor.
     * @param EntityManager $em
     * @param EventDispatcherInterface $eventDispatcher
     * @param FileRunner $fileRunner
     * @param FileProcessor $fileProcessor
     * @param Analyser $analyser
     * @param FilepathGenerator $filepathGenerator
     * @internal param ImageExif $imageExif
     * @internal param Logger $logger
     */
    public function __construct(
        EntityManager $em,
        EventDispatcherInterface $eventDispatcher,
        FileRunner $fileRunner,
        FileProcessor $fileProcessor,
        Analyser $analyser,
        FilepathGenerator $filepathGenerator
    )
    {
        $this->em = $em;
        $this->fileRunner = $fileRunner;
        $this->fileProcessor = $fileProcessor;
        $this->analyser = $analyser;
        $this->eventDispatcher = $eventDispatcher;
        $this->filepathGenerator = $filepathGenerator;
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
     * @param FileInfo $file
     * @return MediaFile
     */
    private function createEntity(FileInfo $file): MediaFile
    {
        $entity = new MediaFile();
        $entity
            ->setType(MediaFile::TYPE_UNKNOWN)
            ->setStatus(MediaFile::STATUS_NEW)
            ->setMimeType($file->getMimeType())
            ->setHash($file->getFileHash())
            ->setPath($this->filepathGenerator->generate($file))
            ->setName($file->getFilename());

        if ($file->getMediaDate()) {
            $entity->setMediaDate($file->getMediaDate());
        } else {
            $entity->setMediaDate($file->getFileDate());
        }

        return $entity;
    }
}
