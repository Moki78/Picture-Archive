<?php

namespace PictureArchiveBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use PictureArchiveBundle\Component\Configuration;
use PictureArchiveBundle\Component\FileInfo;
use PictureArchiveBundle\Entity\MediaFile;
use PictureArchiveBundle\Service\Indexer\FileRunner;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 *
 * @package PictureArchiveBundle\Service
 * @author Moki <picture-archive@mokis-welt.de>
 */
class Indexer
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
     * @var EntityManager
     */
    private $em;

    /**
     * Processor constructor.
     * @param EntityManager $em
     * @param Configuration $configuration
     * @param FileRunner $fileRunner
     */
    public function __construct(
        EntityManager $em,
        Configuration $configuration,
        FileRunner $fileRunner
    )
    {
        $this->em = $em;
        $this->fileRunner = $fileRunner;
        $this->configuration = $configuration;
    }

    /**
     * @param ProgressBar $progressBar
     * @throws \InvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     */
    public function run(ProgressBar $progressBar): void
    {
        $this->fileRunner->loadFiles();

        $progressBar->start($this->fileRunner->count());


        $repository = $this->em->getRepository('PictureArchiveBundle:MediaFile');

        array_map(
            function (MediaFile $mediaFile) {
                $mediaFile->setStatus(MediaFile::STATUS_NOT_FOUND);
            },
            $repository->findAll()
        );
        $this->em->flush();

        foreach ($this->fileRunner->getFileCollection() as $file) {
            $mediaFiles = $repository->findByHash($file->getFileHash());

            $filepath = str_replace(
                $this->configuration->getArchiveBaseDirectory() . '/',
                '',
                $file->getPathname()
            );

            if ($mediaFiles->count() > 1) {
                // more than one file hash found, must be a conflicted
                $mediaFile = null;

                /** @var MediaFile $storedFile */
                foreach ($mediaFiles as $storedFile) {
                    if ($filepath === $storedFile->getPath()) {
                        $mediaFile = $storedFile;
                        break;
                    }
                }

                $mediaFiles->map(function (MediaFile $mediaFile) {
                    $mediaFile->setStatus(MediaFile::STATUS_DUPLICATE);
                });

                if (!$mediaFile instanceof MediaFile) {
                    $mediaFile = $this->createEntity($file);
                    $mediaFile->setStatus(MediaFile::STATUS_DUPLICATE);
                    $mediaFile->setPath($filepath);

                    $this->em->persist($mediaFile);
                }
            } else {
                // existing file
                $mediaFile = $mediaFiles->offsetGet(0);
                if ($mediaFile instanceof MediaFile) {
                    // check for conflict


                    if ($filepath !== $mediaFile->getPath()) {
                        $mediaFile->setStatus(MediaFile::STATUS_DUPLICATE);

                        // same hash, but different filepath
                        $newMediaFile = $this->createEntity($file);
                        $newMediaFile->setPath($filepath);
                        $newMediaFile->setStatus(MediaFile::STATUS_DUPLICATE);

                        $this->em->persist($newMediaFile);
                    } else {
                        // single file
                        $mediaFile->setStatus(MediaFile::STATUS_IMPORTED);
                    }
                } else {
                    // new file
                    $mediaFile = $this->createEntity($file);
                    $mediaFile->setStatus(MediaFile::STATUS_IMPORTED);
                    $mediaFile->setPath($filepath);

                    $this->em->persist($mediaFile);
                }
            }

            $this->em->flush();

            $progressBar->advance();
        }

        $progressBar->finish();
    }

    /**
     * @param FileInfo $file
     * @return MediaFile
     */
    private function createEntity(FileInfo $file): MediaFile
    {
        $mediaFile = new MediaFile();
        $mediaFile
            ->setHash($file->getFileHash())
            ->setMediaDate($file->getMediaDate())
            ->setType(MediaFile::TYPE_UNKNOWN)
            ->setMimeType($file->getMimeType())
            ->setName($file->getFilename());

        if ($file->getMediaDate()) {
            $mediaFile->setMediaDate($file->getMediaDate());
        } else {
            $mediaFile->setMediaDate($file->getFileDate());
        }

        return $mediaFile;
    }

    /**
     * @return ArrayCollection
     */
    public function getDuplicates(): ArrayCollection
    {
        return $this->em->getRepository('PictureArchiveBundle:MediaFile')->findDuplicateHashes();

    }

    /**
     * @param MediaFile $file
     * @return bool
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     */
    public function deleteConflictedFile(MediaFile $file): bool
    {
        $filepath = rtrim($this->configuration->getArchiveBaseDirectory()) . '/' . $file->getPath();
        if (!file_exists($filepath) || unlink($filepath)) {
            $this->em->remove($file);
            return true;
        }
        return false;
    }
}
