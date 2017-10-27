<?php

namespace PictureArchiveBundle\Component\Report;

use PictureArchiveBundle\Component\FileInfo;
use PictureArchiveBundle\Event\ImportAnalysisFailedEvent;
use PictureArchiveBundle\Event\ImportErrorEvent;
use PictureArchiveBundle\Event\ImportSaveFailedEvent;
use PictureArchiveBundle\Event\ImportSuccessEvent;

/**
 *
 * @package PictureArchiveBundle\Component\Report
 * @author Moki <picture-archive@mokis-welt.de>
 */
class Stats implements ReportInterface
{
    /**
     * @var int
     */
    private $success = 0;

    /**
     * @var int
     */
    private $failed = 0;

    /**
     * @var int
     */
    private $error = 0;

    /**
     * @var int
     */
    private $amount = 0;

    public function initialize(): void
    {

    }

    public function write(FileInfo $fileInfo, ?string $status, ?string $message): void
    {
        $this->amount++;

        switch ($status) {
            case ImportSaveFailedEvent::STATUS:
            case ImportAnalysisFailedEvent::STATUS:
                $this->failed++;
                return;
            case ImportSuccessEvent::STATUS:
                $this->success++;
                return;
            case ImportErrorEvent::STATUS:
                $this->error++;
                return;
        }
    }

    public function finish(): void
    {
    }


    /**
     * @return int
     */
    public function getSuccess(): int
    {
        return $this->success;
    }

    /**
     * @return int
     */
    public function getFailed(): int
    {
        return $this->failed;
    }

    /**
     * @return int
     */
    public function getError(): int
    {
        return $this->error;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }


}
