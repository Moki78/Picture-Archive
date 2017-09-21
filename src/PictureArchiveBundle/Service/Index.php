<?php

namespace PictureArchiveBundle\Service;

use Doctrine\ORM\EntityManager;
use PictureArchiveBundle\Component\Configuration;
use PictureArchiveBundle\Component\FileInfo;
use PictureArchiveBundle\Entity\ImportFile;
use PictureArchiveBundle\Entity\MediaFile;
use PictureArchiveBundle\Service\Index\FileRunner;
use PictureArchiveBundle\Util\ImageExif;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 *
 * @package PictureArchiveBundle\Service
 * @author Moki <picture-archive@mokis-welt.de>
 */
class Index
{
    /**
     * @var FileRunner
     */
    private $fileRunner;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ImageExif
     */
    private $imageExifService;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Processor constructor.
     * @param EntityManager $em
     * @param EventDispatcherInterface $eventDispatcher
     * @param FileRunner $fileRunner
     * @param Configuration $configuration
     * @param ImageExif $imageExif
     */
    public function __construct(
        EntityManager $em,
        EventDispatcherInterface $eventDispatcher,
        FileRunner $fileRunner,
        Configuration $configuration,
        ImageExif $imageExif
    )
    {
        $this->em = $em;
        $this->fileRunner = $fileRunner;
        $this->configuration = $configuration;
        $this->imageExifService = $imageExif;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param ProgressBar $progressBar
     * @throws \InvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function run(ProgressBar $progressBar): void
    {
        $this->fileRunner->loadFiles();

        $progressBar->start($this->fileRunner->count());

        $repository = $this->em->getRepository('PictureArchiveBundle:MediaFile');
        foreach ($this->fileRunner as $file) {
            $mediaFile = $repository->findByHash($file->getFileHash());

            if (!$mediaFile) {
                $mediaFile = $this->createEntity($file);

                $this->em->persist($mediaFile);
            }
        }
        $this->em->flush();
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
}
