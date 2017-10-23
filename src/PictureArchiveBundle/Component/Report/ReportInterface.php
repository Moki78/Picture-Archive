<?php

namespace PictureArchiveBundle\Component\Report;

/**
 * Interface ReportInterface
 * @package PictureArchiveBundle\Component\Report
 */
interface ReportInterface
{
    public function initialize(): void;

    /**
     * @param array $item
     */
    public function write(array $item): void;
}
