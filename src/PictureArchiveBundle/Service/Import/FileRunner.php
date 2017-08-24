<?php

namespace PictureArchiveBundle\Service\Import;

use ArrayIterator;
use finfo;
use PictureArchiveBundle\Component\Configuration;
use PictureArchiveBundle\Entity\ImportFile;

/**
 *
 * @package PictureArchiveBundle\Import
 * @author Moki <picture-archive@mokis-welt.de>
 */
class FileRunner implements \Iterator, \Countable
{
    /**
     * @var array
     */
    private $excludeList = array();

    /**
     * @var finfo
     */
    private $finfo;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ArrayIterator
     */
    private $fileIterator;

    /**
     * FileRunner constructor.
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->finfo = new finfo(FILEINFO_MIME); // return mime type ala mimetype extension
        $this->configuration = $configuration;
    }

    /**
     * @param array $excludeList
     * @return $this
     */
    public function setExcludeList(array $excludeList)
    {
        $this->excludeList = $excludeList;

        return $this;
    }

    /**
     * @param $exclude
     * @return $this
     */
    public function addToExcludeList($exclude)
    {
        $this->excludeList[] = $exclude;

        return $this;
    }

    /**
     * @return ImportFile
     * @throws \RuntimeException
     */
    public function current(): ImportFile
    {
        return $this->getCurrentImportFile();
    }

    public function next(): void
    {
        $this->fileIterator->next();
    }

    /**
     * @return int
     */
    public function key(): int
    {
        return $this->fileIterator->key();
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return $this->fileIterator->valid();
    }

    public function rewind()
    {
        $this->fileIterator->rewind();
    }

    /**
     * @return ArrayIterator
     */
    public function getFileIterator(): \ArrayIterator
    {
        return $this->fileIterator;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->fileIterator->count();
    }

    /**
     *
     * @throws \InvalidArgumentException
     */
    public function loadFiles()
    {
        $fileList = [];

        $directory = new \RecursiveDirectoryIterator(
            $this->configuration->getImportBaseDirectory(),
            \RecursiveDirectoryIterator::SKIP_DOTS
        );

        foreach (new \RecursiveIteratorIterator($directory) as $file) {
            if ($this->isValidFile($file)) {
                $fileList[] = $file;
            }
        }

        $this->fileIterator = new ArrayIterator($fileList);
    }

    /**
     * @param \SplFileInfo $file
     * @return bool
     */
    private function isValidFile(\SplFileInfo $file): bool
    {
        if ($file->isDir() || $file->isLink()) {
            return false;
        }

        foreach ($this->excludeList as $excludeItem) {
            if (false !== strpos($file->getPathname(), $excludeItem)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return ImportFile
     * @throws \RuntimeException
     */
    private function getCurrentImportFile(): ImportFile
    {
        $file = $this->fileIterator->current();

        $fileDate = new \DateTime();
        $fileDate->setTimestamp($file->getMTime());

        $mimeType = $this->getMime($file->getPathname());

        $importFile = new ImportFile();
        $importFile
            ->setFile($file)
            ->setFileDate($fileDate)
            ->setMimeType($mimeType);

        return $importFile;
    }

    /**
     * @param string $filepath
     * @return string
     */
    private function getMime($filepath): string
    {
        return $this->finfo->file($filepath);
    }
}
