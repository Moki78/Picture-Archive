<?php

namespace PictureArchiveBundle\Component\Report;

/**
 * Interface ReportInterface
 * @package PictureArchiveBundle\Component\Report
 */
interface ReportInterface
{
    /**
     * @param array $item
     */
    public function write(array $item): void;
}
