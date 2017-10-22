<?php

namespace PictureArchiveBundle\Service\Import;

use PictureArchiveBundle\Component\Configuration;
use PictureArchiveBundle\Component\FileInfo;
use PictureArchiveBundle\Event\ImportAnalysisFailedEvent;
use PictureArchiveBundle\Event\ImportFileEvent;

/**
 *
 * @package PictureArchiveBundle\Service\Import
 * @author Moki <picture-archive@mokis-welt.de>
 */
class FailedHandler
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * Report constructor.
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param ImportFileEvent $importEvent
     * @throws \RuntimeException
     */
    public function moveFile(ImportFileEvent $importEvent): void
    {
        if ($importEvent instanceof ImportAnalysisFailedEvent) {
            if (FileInfo::STATUS_WAIT === $importEvent->getFileInfo()->getStatus()) {
                return;
            }
        }

        $importFile = $importEvent->getFileInfo();

        $failedDirectory = new \SplFileInfo($this->configuration->getImportFailedDirectory());

        if (!$failedDirectory->isDir() && !mkdir($failedDirectory->getPathname(), 0775, true)) {
            throw new \RuntimeException("could not create directory '{$failedDirectory->getPathname()}'");
        }

        if (!$failedDirectory->isWritable()) {
            throw new \RuntimeException("directory '{$failedDirectory->getPathname()}' is not writable");
        }

        $targetPathname = $failedDirectory->getPathname() . '/' . $importFile->getFilename();

        rename($importFile->getPathname(), $targetPathname);
    }
}
