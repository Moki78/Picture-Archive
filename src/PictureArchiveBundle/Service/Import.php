<?php

namespace PictureArchiveBundle\Service;

use Doctrine\ORM\EntityManager;
use PictureArchiveBundle\Component\FileInfo;
use PictureArchiveBundle\Component\FilepathGenerator;
use PictureArchiveBundle\Entity\MediaFile;
use PictureArchiveBundle\Event\ImportAnalysisFailedEvent;
use PictureArchiveBundle\Event\ImportErrorEvent;
use PictureArchiveBundle\Event\ImportFileEvent;
use PictureArchiveBundle\Event\ImportFinishEvent;
use PictureArchiveBundle\Event\ImportInitializeEvent;
use PictureArchiveBundle\Event\ImportSaveFailedEvent;
use PictureArchiveBundle\Event\ImportSuccessEvent;
use PictureArchiveBundle\Events;
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
        $this->eventDispatcher->dispatch(Events::IMPORT_INITIALIZE, new ImportInitializeEvent());

        $this->fileProcessor->initialize();

        $this->fileRunner->loadFiles();

        $progressBar->start($this->fileRunner->count());

        foreach ($this->fileRunner as $importFile) {
            $progressBar->advance();

            $this->eventDispatcher->dispatch(Events::IMPORT_FILE, new ImportFileEvent($importFile));

            try {
                $importFile = $this->analyser->analyse($importFile);
                if (FileInfo::STATUS_INVALID === $importFile->getStatus()) {
                    $this->eventDispatcher->dispatch(
                        Events::IMPORT_ANALYSIS_FAILED,
                        new ImportAnalysisFailedEvent($importFile, $importFile->getStatusMessage())
                    );
                    continue;
                }

                $mediaFile = $this->createEntity($importFile);

                $status = $this->fileProcessor->saveFile($mediaFile, $importFile->getPathname());

                if (!$status) {
                    // could not rename file
                    $this->eventDispatcher->dispatch(
                        Events::IMPORT_SAVE_FAILED,
                        new ImportSaveFailedEvent($importFile, $mediaFile, 'rename failed')
                    );

                    continue;
                }

                $mediaFile->setStatus(MediaFile::STATUS_IMPORTED);

                // save file
                $this->em->persist($mediaFile);
                $this->em->flush();

                $this->eventDispatcher->dispatch(
                    Events::IMPORT_SUCCESS,
                    new ImportSuccessEvent($importFile, $mediaFile)
                );

            } catch (\Exception $e) {
                // log Exception
                $this->eventDispatcher->dispatch(
                    Events::IMPORT_ERROR,
                    new ImportErrorEvent($importFile, $e)
                );


                continue;
            }
        }
        $progressBar->finish();

        $this->eventDispatcher->dispatch(
            Events::IMPORT_FINISH,
            new ImportFinishEvent()
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
            ->setType($file->getFileType())
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
