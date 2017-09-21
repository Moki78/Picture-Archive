<?php

namespace PictureArchiveBundle\Service\Import\Analyser;

use PictureArchiveBundle\Component\Configuration;
use PictureArchiveBundle\Component\FileInfo;
use PictureArchiveBundle\Service\Import\AnalyserInterface;

/**
 *
 * @package PictureArchiveBundle\Service\Import
 * @author Moki <picture-archive@mokis-welt.de>
 */
class FileMinAge implements AnalyserInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * FileMinAge constructor.
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param FileInfo $fileInfo
     * @return bool
     */
    public function analyse(FileInfo $fileInfo): bool
    {
        if ($fileInfo->getFileDate()->getTimestamp() + $this->configuration->getImportMinimumFileAge() < time()) {
            $fileInfo->setStatus(FileInfo::STATUS_VALID);
            return true;
        }

        $fileInfo->setStatus(FileInfo::STATUS_WAIT);
        $fileInfo->setStatusMessage('low fileage, ignore on import');

        return false;
    }
}
