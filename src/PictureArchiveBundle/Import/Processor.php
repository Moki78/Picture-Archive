<?php

namespace PictureArchiveBundle\Import;

use Doctrine\ORM\EntityManager;
use PictureArchiveBundle\Entity\File as FileEntity;
use PictureArchiveBundle\Entity\File;
use PictureArchiveBundle\Index\FileProcessor;
use PictureArchiveBundle\Util\FileHash\Md5;
use PictureArchiveBundle\Util\FileHashInterface;
use PictureArchiveBundle\Util\FileScanner;
use PictureArchiveBundle\Util\ImageExif;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Console\Helper\ProgressBar;

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
     * @var FileProcessor
     */
    private $fileProcessor;

    /**
     * @var string
     */
    private $config;

    /**
     * Processor constructor.
     * @param Logger $logger
     * @param EntityManager $em
     * @param FileScanner $fileScanner
     * @param FileProcessor $fileProcessor
     * @param ImageExif $imageDataService
     * @param FileHashInterface $fileHash
     * @param array $config
     */
    public function __construct(
        Logger $logger,
        EntityManager $em,
        FileScanner $fileScanner,
        FileProcessor $fileProcessor,
        ImageExif $imageDataService,
        FileHashInterface $fileHash,
        array $config
    ) {

        $this->logger = $logger;
        $this->scanner = $fileScanner;
        $this->fileProcessor = $fileProcessor;
        $this->em = $em;
        $this->config = $config;
        $this->imageDataService = $imageDataService;
        $this->fileHash = $fileHash;

    }

    /**
     * @param ProgressBar $progressBar
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function run(ProgressBar $progressBar)
    {
        $this->logger->info('run import processor');

        $files = $this->scanner->getFiles();

        $progressBar->start($files->count());

        $stats = array('imported' => 0, 'failed' => 0);

        foreach ($files as $importFile) {
            $progressBar->advance();

            try {
                $this->logger->info("process '{$importFile->getFilepath()}'");

                if (!$this->checkFileAge($importFile)) {
                    $this->logger->info('low fileage, ignore on import');
                    continue;
                }

                $importFile->setFileType($this->getFileType($importFile->getMimeType()));

                $entity = new FileEntity();
                $entity
                    ->setStatus(FileEntity::STATUS_NEW)
                    ->setMimeType($importFile->getMimeType())
                    ->setHash(
                        $this->fileHash->hash($importFile->getFilepath())
                    );


                $createDate = $this->imageDataService->getCreationDate($importFile->getFilepath());
                if ($createDate) {
                    $entity->setMediaDate($createDate);
                    $importFile->setCreateDate($createDate);
                } else {
                    $entity->setMediaDate($importFile->getCreateDate());
                }


                $fullFilepath = $this->getGenerateFilename($importFile);
                $filepath = explode('/', $fullFilepath);

                $entity->setFilepath(
                    ltrim(str_replace($this->config['base_directory'], '', $fullFilepath), '/')
                );
                $entity->setFilename(end($filepath));

                $this->checkFile($entity);

                $this->fileProcessor->saveFile($importFile->getFilepath(), $fullFilepath);

                $entity->setStatus(FileEntity::STATUS_IMPORTED);

                // save file
                $this->em->persist($entity);
                $this->em->flush();

                $stats['imported']++;

            } catch (\Exception $e) {
                // log Exception
                $this->logger->error("failed to proceed file: '{$importFile->getFilepath()}'");
                $this->logger->error($e->getMessage());

                $this->fileProcessor->saveFailedFile(
                    $importFile->getFilepath(),
                    $this->config['failed_directory']
                );

                $stats['failed']++;
                continue;
            }
        }

        $this->logger->info($stats['imported'] . ' files imported');
        $this->logger->info($stats['failed'] . ' files failed');
    }

    /**
     * @param ImportFile $file
     * @return string
     */
    private function getGenerateFilename(ImportFile $file)
    {
        $filepath = sprintf(
            '%s/%s/%s/%s_%s',
            $this->config['base_directory'],
            $this->config['type_path'][$file->getFileType()],
            $file->getCreateDate()->format('Y/m'),
            $file->getCreateDate()->format('Ymd-His'),
            $file->getFilename()
        );

        return $filepath;
    }

    /**
     * @param File $entity
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \PictureArchiveBundle\Import\Exception
     */
    private function checkFile(FileEntity $entity)
    {
        $repository = $this->em->getRepository('PictureArchiveBundle\:File');
        $result = $repository->findByHash($entity->getHash());

        if (0 === $result->count()) {
            // no hash found, check for existing file
            $filepath = str_replace($this->config['base_directory'], '', $entity->getFilepath());
            if (!$repository->findByFilepath($filepath)) {
                return true;
            }


            throw new Exception(
                sprintf(
                    "Filepath '%s' already exists",
                    $entity->getFilepath()
                ),
                Exception::FILE_EXISTS
            );
        }

        throw new Exception(
            sprintf(
                "File hash '%s' already exists",
                $entity->getHash()
            ),
            Exception::HASH_EXISTS
        );
    }

    /**
     * @param $mimeType
     * @return int|string
     * @throws Exception
     */
    private function getFileType($mimeType)
    {
        foreach ($this->config['supported_types'] as $type => $check) {
            if (preg_match($check, $mimeType)) {
                return $type;
            }
        }

        throw new Exception(
            "file type $mimeType is not supported",
            Exception::FILE_UNSUPPORTED
        );
    }

    /**
     * @param ImportFile $file
     * @return bool
     */
    private function checkFileAge(ImportFile $file)
    {
        return ($file->getCreateDate()->getTimestamp() + $this->config['minimum_fileage'] < time());
    }
}
