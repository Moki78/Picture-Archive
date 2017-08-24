<?php

namespace PictureArchiveBundle\Service\Import\Analyser;

use PictureArchiveBundle\Component\Configuration;
use PictureArchiveBundle\Entity\ImportFile;
use PictureArchiveBundle\Repository\FilesRepository;
use PictureArchiveBundle\Service\Import\AnalyserInterface;
use PictureArchiveBundle\Util\FileHashInterface;

/**
 *
 * @package PictureArchiveBundle\Service\Import\Analyser
 * @author Moki <picture-archive@mokis-welt.de>
 */
class FileExists implements AnalyserInterface
{
    /**
     * @var Configuration
     */
    private $configuration;
    /**
     * @var FilesRepository
     */
    private $repository;
    /**
     * @var FileHashInterface
     */
    private $hash;

    /**
     * FileMinAge constructor.
     *
     * @param Configuration $configuration
     * @param FileHashInterface $hash
     * @param FilesRepository $repository
     */
    public function __construct(Configuration $configuration, FileHashInterface $hash, FilesRepository $repository)
    {
        $this->configuration = $configuration;
        $this->repository = $repository;
        $this->hash = $hash;
    }

    /**
     * @param ImportFile $fileimportFile
     * @return bool
     */
    public function analyse(ImportFile $fileimportFile)
    {
        $filepath = $fileimportFile->getFile()->getPathname();
        $hash = $this->hash->hash($filepath);

        $result = $this->repository->findByHash($hash);

        if (0 === $result->count()) {
            // no hash found, check for existing file
            $filepath = str_replace(
                $this->configuration->getArchiveBaseDirectory(),
                '',
                $filepath);


            if (!$this->repository->findByFilepath($filepath)) {
                $fileimportFile->setStatus(ImportFile::STATUS_VALID);
                return true;
            }

            $fileimportFile->setStatus(ImportFile::STATUS_INVALID);
            $fileimportFile->setStatusMessage("Filepath '{$filepath}' already exists");

            return false;
        }

        $fileimportFile->setStatus(ImportFile::STATUS_INVALID);
        $fileimportFile->setStatusMessage("File hash '{$hash}' already exists");

        return false;
    }
}
