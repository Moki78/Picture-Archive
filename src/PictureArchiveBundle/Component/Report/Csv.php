<?php

namespace PictureArchiveBundle\Component\Report;

use PictureArchiveBundle\Component\FileInfo;

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

    }

    /**
     * @param FileInfo $fileInfo
     * @param null|string $status
     * @param null|string $message
     */
    public function write(FileInfo $fileInfo, ?string $status, ?string $message): void
    {
        $this->initializeFile();

        $this->file->fputcsv([
            date('Y-m-d H:i:s'),
            $fileInfo->getPathname(),
            $status,
            $message
        ]);
    }

    public function finish(): void
    {

    }

    private function initializeFile(): void
    {
        if ($this->file) {
            return;
        }

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
}
