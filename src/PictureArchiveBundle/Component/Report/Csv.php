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

    /**
     * @var string|null
     */
    private $outputDirectory;

    /**
     * Csv constructor.
     * @param null $outputDirectory
     */
    public function __construct($outputDirectory = null)
    {
        $this->outputDirectory = $outputDirectory;
    }

    public function initialize(): void
    {
        $file = 'php://stdout';
        if ($this->outputDirectory && is_dir($this->outputDirectory)) {
            $file = sprintf('%s/reporter-%s.csv', $this->outputDirectory, date('YmdHis'));
        }
        $this->file = new \SplFileObject($file, 'a');

        $this->file->fputcsv([
            'datetime',
            'import file',
            'status',
            'message'
        ]);
    }

    /**
     * @param array $item
     */
    public function write(array $item): void
    {
        array_unshift($item, date('Y-m-d H:i:s'));
        $this->file->fputcsv($item);
    }
}
