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
class MimeType implements AnalyserInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * MimeType constructor.
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
        foreach ($this->configuration->getImportSupportedTypes() as $type => $check) {
            if (preg_match($check, $fileInfo->getMimeType())) {
                $fileInfo->setStatus(FileInfo::STATUS_VALID);
                return true;
            }
        }

        $fileInfo->setStatus(FileInfo::STATUS_INVALID);
        $fileInfo->setStatusMessage("file type {$fileInfo->getMimeType()} is not supported");

        return false;
    }
}
