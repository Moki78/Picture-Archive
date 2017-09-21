<?php

namespace PictureArchiveBundle\Service\Index;

use ArrayIterator;
use finfo;
use PictureArchiveBundle\Component\Configuration;
use PictureArchiveBundle\Entity\ImportFile;
use PictureArchiveBundle\Util\FileHashInterface;
use SplFileInfo;

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
     * @var FileHashInterface
     */
    private $hashService;

    /**
     * FileRunner constructor.
     * @param Configuration $configuration
     * @param FileHashInterface $hashService
     */
    public function __construct(Configuration $configuration, FileHashInterface $hashService)
    {
        $this->finfo = new finfo(FILEINFO_MIME); // return mime type ala mimetype extension
        $this->configuration = $configuration;
        $this->hashService = $hashService;
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

    /**
     * @return ImportFile
     * @throws \RuntimeException
     */
    private function getCurrentImportFile(): ImportFile
    {

        /** @var SplFileInfo $file */
        $file = $this->fileIterator->current();

        $fileDate = new \DateTime();
        $fileDate->setTimestamp($file->getMTime());

        $importFile = new ImportFile();
        $importFile
            ->setFile($file)
            ->setFileDate($fileDate)
            ->setFileHash($this->hashService->hash($file->getPathname()))
            ->setMimeType($this->finfo->file($file->getPathname()));

        return $importFile;
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
}
