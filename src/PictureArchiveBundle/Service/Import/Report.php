<?php

namespace PictureArchiveBundle\Service\Import;

use PictureArchiveBundle\Component\Configuration;
use PictureArchiveBundle\Event\EventInterface;
use PictureArchiveBundle\Event\ImportFileEvent;
use PictureArchiveBundle\Event\ImportFinishEvent;
use PictureArchiveBundle\Event\ImportInitializeEvent;

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
     * @param ImportInitializeEvent $importEvent
     */
    public function startImport(ImportInitializeEvent $importEvent): void
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
     * @param ImportFinishEvent $importEvent
     */
    public function finishImport(ImportFinishEvent $importEvent): void
    {

    }

    /**
     * @param ImportFileEvent $event
     */
    public function importFile(ImportFileEvent $event): void
    {
        $status = null;
        $message = '';
        if ($event instanceof EventInterface) {
            $status = $event->getStatus();
            $message = $event->getMessage();
        }


        $this->writeToReporter([
            $event->getFileInfo()->getPathname(),
            $status,
            $message,
        ]);
    }

}
