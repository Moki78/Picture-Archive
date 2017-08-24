<?php

namespace PictureArchiveBundle\Service\Index;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use PictureArchiveBundle\Entity\MediaFile;
use PictureArchiveBundle\Entity\MediaFile as FileEntity;
use PictureArchiveBundle\Util\FileHashInterface;
use PictureArchiveBundle\Util\FileScanner;
use PictureArchiveBundle\Util\ImageExif;
use Symfony\Bridge\Monolog\Logger;

class Processor
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var FileScanner
     */
    private $scanner;

    /**
     * @var ImageExif
     */
    private $imageDataService;

    /**
     * @var FileHashInterface
     */
    private $fileHash;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var string
     */
    private $config;

    /**
     * Processor constructor.
     * @param Logger $logger
     * @param EntityManager $em
     * @param FileScanner $fileScanner
     * @param ImageExif $imageDataService
     * @param FileHashInterface $fileHash
     * @param array $config
     */
    public function __construct(
        Logger $logger,
        EntityManager $em,
        FileScanner $fileScanner,
        ImageExif $imageDataService,
        FileHashInterface $fileHash,
        array $config
    ) {
        $this->logger = $logger;
        $this->scanner = $fileScanner;
        $this->imageDataService = $imageDataService;
        $this->fileHash = $fileHash;
        $this->em = $em;
        $this->config = $config;

    }

    /**
     *
     */
    public function run(): void
    {
        $this->logger->info('read directory ...');

        $files = $this->scanner->getFiles();



        $this->logger->info('process ' . $files->count() . ' files ...');

        $entityCollection = new ArrayCollection();
        $counter = 0;
        foreach ($files as $file) {
            $this->logger->debug("process '{$file->getFilepath()}'");

            $filepath = ltrim(str_replace($this->config['base_directory'], '', $file->getFilepath()), '/');

            $entity = new FileEntity();
            $entity
                ->setPath($filepath)
                ->setName($file->getFilename())
                ->setStatus(FileEntity::STATUS_IMPORTED);

            $entity->setMimeType($file->getMimeType());
            $entity->setHash($this->fileHash->hash($file->getFilepath()));

            $createDate = $this->imageDataService->getCreationDate($file->getFilepath());
            if ($createDate) {
                $entity->setMediaDate($createDate);
            } else {
                $entity->setMediaDate($file->getCreateDate());
            }

            $entityCollection->forAll(
                function ($key, FileEntity $collectionItem) use ($entity) {
                    if ($entity->getHash() == $collectionItem->getHash()) {
                        $entity->setStatus(FileEntity::STATUS_CONFLICT);
                        $collectionItem->setStatus(FileEntity::STATUS_CONFLICT);
                    }

                    return true;
                }
            );

            $this->em->persist($entity);
            $entityCollection->add($entity);

            if (++$counter % 100 === 0) {
                $this->logger->info($counter . " files processed ...");
            }

        }

        $this->em->getRepository('PictureArchiveBundle\:File')->removeAll();
        $this->em->flush();
    }

    /**
     * @return ArrayCollection|FileEntity[]
     */
    public function getConflictedHashes()
    {
        return $this->em->getRepository('PictureArchiveBundle\:File')->findConflictedHashes();
    }

    /**
     * @param MediaFile $file
     * @return bool
     */
    public function deleteConflictedFile(MediaFile $file)
    {
        $filepath = rtrim($this->config['base_directory']) . '/' . $file->getPath();
        if (!file_exists($filepath) || unlink($filepath)) {
            $this->em->remove($file);
            return true;
        }
        return false;
    }

    /**
     * @return ArrayCollection|FileEntity[]
     */
    public function getFiles()
    {
        return new ArrayCollection(
            $this->em->getRepository('PictureArchiveBundle\:File')->findBy(array(), array('filepath' => 'ASC'))
        );
    }
}
