<?php

namespace PictureArchiveBundle\Service;

use Doctrine\ORM\EntityManager;
use PictureArchiveBundle\Component\Configuration;
use PictureArchiveBundle\Entity\MediaFile as MediaFileEntity;
use PictureArchiveBundle\Entity\ImportFile;
use PictureArchiveBundle\Service\Import\AnalyseEvent;
use PictureArchiveBundle\Service\Import\Analyser;
use PictureArchiveBundle\Service\Import\Exception;
use PictureArchiveBundle\Service\Import\FileRunner;
use PictureArchiveBundle\Service\Index\FileProcessor;
use PictureArchiveBundle\Util\FileHashInterface;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 *
 * @package PictureArchiveBundle\Import
 * @author Moki <picture-archive@mokis-welt.de>
 */
class Import
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var FileRunner
     */
    private $fileRunner;

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
     * @var Configuration
     */
    private $configuration;
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;
    /**
     * @var Analyser
     */
    private $analyser;

    /**
     * Processor constructor.
     * @param EntityManager $em
     * @param FileRunner $fileRunner
     * @param FileProcessor $fileProcessor
     * @param Analyser $analyser
     * @param FileHashInterface $fileHash
     * @param Configuration $configuration
     * @param Logger $logger
     */
    public function __construct(
        EntityManager $em,
        FileRunner $fileRunner,
        FileProcessor $fileProcessor,
        Analyser $analyser,
        FileHashInterface $fileHash,
        Configuration $configuration,
        Logger $logger
    )
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->fileRunner = $fileRunner;
        $this->fileProcessor = $fileProcessor;
        $this->fileHash = $fileHash;
        $this->configuration = $configuration;
        $this->analyser = $analyser;
    }

    /**
     * @param ProgressBar $progressBar
     * @return bool
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function run(ProgressBar $progressBar): bool
    {
        $this->logger->info('run import processor');

        $this->fileRunner->loadFiles();

        $progressBar->start($this->fileRunner->count());

        $stats = array('imported' => 0, 'failed' => 0);

        foreach ($this->fileRunner as $importFile) {
            $progressBar->advance();

            try {
                $this->logger->info("process '{$importFile->getFile()->getPathname()}'");

                $importFile = $this->analyser->analyse($importFile);
                if (ImportFile::STATUS_INVALID === $importFile->getStatus()) {
                    continue;
                }

                $mediaFileEntity = $this->createEntity($importFile);


//                $this->fileProcessor->saveFile($importFile->getFile()->getPathname(), $fullFilepath);

                $mediaFileEntity->setStatus(MediaFileEntity::STATUS_IMPORTED);

                // save file
                $this->em->persist($mediaFileEntity);
                $this->em->flush();

                $stats['imported']++;

            } catch (\Exception $e) {
                // log Exception
                $this->logger->error("failed to proceed file: '{$importFile->getFile()->getPathname()}'");
                $this->logger->error($e->getMessage());

//                $this->fileProcessor->saveFailedFile(
//                    $importFile->getFilepath(),
//                    $this->config['failed_directory']
//                );

                $stats['failed']++;
                continue;
            }
        }
        $progressBar->finish();


        $this->logger->info($stats['imported'] . ' files imported');
        $this->logger->info($stats['failed'] . ' files failed');

        return true;
    }

    /**
     * @param ImportFile $importFile
     * @return MediaFileEntity
     */
    private function createEntity(ImportFile $importFile): MediaFileEntity
    {
        $entity = new MediaFileEntity();
        $entity
            ->setStatus(MediaFileEntity::STATUS_NEW)
            ->setMimeType($importFile->getMimeType())
            ->setHash($this->fileHash->hash($importFile->getFile()->getPathname()))
            ->setMediaDate($importFile->getMediaDate())
            ->setPath($this->generateFilename($importFile))
            ->setName(end($filepath));

        return $entity;
    }

    /**
     * @param ImportFile $importFile
     * @return string
     */
    private function generateFilename(ImportFile $importFile): string
    {
        $subDirectories = $this->configuration->getArchiveFiletypeSubdirectory();

        if (array_key_exists($importFile->getFileType(), $subDirectories)) {
            $filepath = sprintf(
                '%s/%s/%s',
                $subDirectories[$importFile->getFileType()],
                $importFile->getFileDate()->format('Y/m'),
                $importFile->getFile()->getFilename()
            );
        } else {
            $filepath = sprintf(
                '%s/%s',
                $importFile->getFileDate()->format('Y/m'),
                $importFile->getFile()->getFilename()
            );
        }

        return $filepath;
    }

    /**
     * @param $mimeType
     * @return int|string
     * @throws Exception
     */
    private function getFileType($mimeType)
    {
        foreach ($this->configuration->getImportSupportedTypes() as $type => $check) {
            if (preg_match($check, $mimeType)) {
                return $type;
            }
        }

        return ImportFile::TYPE_UNKNOWN;
    }
}
