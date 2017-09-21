<?php

namespace PictureArchiveBundle\Service\Import;

use PictureArchiveBundle\Component\Configuration;
use PictureArchiveBundle\Event\ImportEvent;

/**
 *
 * @package PictureArchiveBundle\Service\Import
 * @author Moki <picture-archive@mokis-welt.de>
 */
class Report
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
     * @param ImportEvent $importEvent
     */
    public function startImport(ImportEvent $importEvent): void
    {
        $this->writeToReporter([
            'import file',
            'status',
            'message'
        ]);
    }

    /**
     * @param array $line
     */
    private function writeToReporter(array $line): void
    {
        foreach ($this->configuration->getReporter() as $reporter) {
            $reporter->write($line);
        }
    }

    /**
     * @param ImportEvent $importEvent
     */
    public function finishImport(ImportEvent $importEvent): void
    {

    }

    /**
     * @param ImportEvent $importEvent
     */
    public function importFile(ImportEvent $importEvent): void
    {
        $this->writeToReporter([
            $importEvent->getFileInfo()->getPathname(),
            $this->getStatusKey($importEvent->getStatus()),
            $importEvent->getMessage(),
        ]);
    }

    /**
     * @param int $status
     * @return string
     */
    private function getStatusKey(int $status): string
    {
        switch ($status) {
            case ImportEvent::STATUS_START:
                return 'start';
            case ImportEvent::STATUS_ANALYSE_FAILED:
                return 'analyse failed';
            case ImportEvent::STATUS_SAVE_FAILED:
                return 'save failed';
            case ImportEvent::STATUS_SUCCESS:
                return 'success';
            case ImportEvent::STATUS_ERROR:
                return 'error';
            case ImportEvent::STATUS_FINISH:
                return 'finish';
        }
        return '';
    }
}
