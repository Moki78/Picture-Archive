<?php

namespace PictureArchiveBundle\Component\Report;

/**
 *
 * @package PictureArchiveBundle\Component\Report
 * @author Moki <picture-archive@mokis-welt.de>
 */
class Text implements ReportInterface
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
     * Text constructor.
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
            $file = sprintf('%s/reporter-%s.txt', $this->outputDirectory, date('YmdHis'));
        }
        $this->file = new \SplFileObject($file, 'a');
    }

    /**
     * @param array $item
     */
    public function write(array $item): void
    {
        $this->file->fwrite(implode(' - ', $item) . PHP_EOL);
    }
}
