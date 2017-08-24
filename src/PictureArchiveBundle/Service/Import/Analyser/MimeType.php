<?php

namespace PictureArchiveBundle\Service\Import\Analyser;

use PictureArchiveBundle\Component\Configuration;
use PictureArchiveBundle\Entity\ImportFile;
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
     * @param ImportFile $fileimportFile
     * @return bool
     */
    public function analyse(ImportFile $fileimportFile)
    {
        foreach ($this->configuration->getImportSupportedTypes() as $type => $check) {
            if (preg_match($check, $fileimportFile->getMimeType())) {
                $fileimportFile->setStatus(ImportFile::STATUS_VALID);
                return true;
            }
        }

        $fileimportFile->setStatus(ImportFile::STATUS_INVALID);
        $fileimportFile->setStatusMessage("file type {$fileimportFile->getMimeType()} is not supported");

        return false;
    }
}
