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
     * @param ImportFile $fileimportFile
     * @return bool
     */
    public function analyse(ImportFile $fileimportFile)
    {
        if ($fileimportFile->getFileDate()->getTimestamp() + $this->configuration->getImportMinimumFileAge() < time()) {
            $fileimportFile->setStatus(ImportFile::STATUS_VALID);
        }

        $fileimportFile->setStatus(ImportFile::STATUS_INVALID);
        $fileimportFile->setStatusMessage('low fileage, ignore on import');

        return false;
    }
}
