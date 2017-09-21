<?php

namespace PictureArchiveBundle\Service\Import\Analyser;

use PictureArchiveBundle\Component\Configuration;
use PictureArchiveBundle\Component\FileInfo;
use PictureArchiveBundle\Repository\MediaFileRepository;
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
     * @var MediaFileRepository
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
     * @param MediaFileRepository $repository
     */
    public function __construct(Configuration $configuration, FileHashInterface $hash, MediaFileRepository $repository)
    {
        $this->configuration = $configuration;
        $this->repository = $repository;
        $this->hash = $hash;
    }

    /**
     * @param FileInfo $fileInfo
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function analyse(FileInfo $fileInfo): bool
    {
        $filepath = $fileInfo->getPathname();
        $hash = $this->hash->hash($filepath);

        $result = $this->repository->findByHash($hash);

        if (0 === $result->count()) {
            // no hash found, check for existing file
            $filepath = str_replace(
                $this->configuration->getArchiveBaseDirectory(),
                '',
                $filepath);


            if (!$this->repository->findByFilepath($filepath)) {
                $fileInfo->setStatus(FileInfo::STATUS_VALID);
                return true;
            }

            $fileInfo->setStatus(FileInfo::STATUS_INVALID);
            $fileInfo->setStatusMessage("Filepath '{$filepath}' already exists");

            return false;
        }

        $fileInfo->setStatus(FileInfo::STATUS_INVALID);
        $fileInfo->setStatusMessage("File hash '{$hash}' already exists");

        return false;
    }
}
