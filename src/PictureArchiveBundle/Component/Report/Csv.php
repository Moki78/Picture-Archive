<?php

namespace PictureArchiveBundle\Component\Report;

/**
 *
 * @package PictureArchiveBundle\Component\Report
 * @author Moki <picture-archive@mokis-welt.de>
 */
class Csv implements ReportInterface
{
    /**
     * @var \SplFileObject
     */
    private $file;

    public function __construct($filepath)
    {
        $this->file = new \SplFileObject($filepath, 'w+');
    }

    public function write(array $item): void
    {
        $this->file->fputcsv($item);
    }
}
