<?php

namespace PictureArchiveBundle\Component\Report;

/**
 *
 * @package PictureArchiveBundle\Component\Report
 * @author Moki <picture-archive@mokis-welt.de>
 */
class Stdout implements ReportInterface
{
    /**
     * @var \SplFileObject
     */
    private $file;

    public function __construct()
    {
        $this->file = new \SplFileObject('php://stdout', 'w');
    }

    public function write(array $item): void
    {
        $this->file->fwrite(implode(' - ', $item) . PHP_EOL);
    }
}
