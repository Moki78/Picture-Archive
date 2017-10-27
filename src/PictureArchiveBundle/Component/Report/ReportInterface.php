<?php

namespace PictureArchiveBundle\Component\Report;

use PictureArchiveBundle\Component\FileInfo;

/**
 * Interface ReportInterface
 * @package PictureArchiveBundle\Component\Report
 */
interface ReportInterface
{
    public function initialize(): void;

    /**
     * @param FileInfo $fileInfo
     * @param null|string $status
     * @param null|string $message
     */
    public function write(FileInfo $fileInfo, ?string $status, ?string $message): void;

    public function finish(): void;
}
