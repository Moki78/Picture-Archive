<?php

namespace PictureArchiveBundle\Service\Import;

use PictureArchiveBundle\Component\Configuration;
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
     * @param ImportInitializeEvent $event
     */
    public function initialize(ImportInitializeEvent $event): void
    {
        foreach ($this->configuration->getReporter() as $reporter) {
            $reporter->initialize();
        }
    }

    /**
     * @param ImportFinishEvent $event
     */
    public function finish(ImportFinishEvent $event): void
    {
        foreach ($this->configuration->getReporter() as $reporter) {
            $reporter->finish();
        }
    }

    /**
     * @param ImportFileEvent $event
     */
    public function sendToReporter(ImportFileEvent $event): void
    {
        foreach ($this->configuration->getReporter() as $reporter) {
            $reporter->write($event->getFileInfo(), $event->getStatus(), $event->getMessage());
        }
    }
}
