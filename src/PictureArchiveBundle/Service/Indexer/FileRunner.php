<?php

namespace PictureArchiveBundle\Service\Indexer;

use Doctrine\Common\Collections\ArrayCollection;
use PictureArchiveBundle\Component\Configuration;
use PictureArchiveBundle\Component\FileInfo;
use PictureArchiveBundle\Component\FileSystem\LoaderAbstract;

/**
 *
 * @package PictureArchiveBundle\Import
 * @author Moki <picture-archive@mokis-welt.de>
 */
class FileRunner implements \Countable
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ArrayCollection
     */
    private $fileCollection;

    /**
     * @var LoaderAbstract
     */
    private $fileLoader;

    /**
     * FileRunner constructor.
     *
     * @param Configuration $configuration
     * @param LoaderAbstract $fileLoader
     */
    public function __construct(Configuration $configuration, LoaderAbstract $fileLoader)
    {
        $this->configuration = $configuration;
        $this->fileLoader = $fileLoader;
    }

    public function count()
    {
        return $this->fileCollection->count();
    }

    /**
     * @return ArrayCollection|FileInfo[]
     */
    public function getFileCollection(): ArrayCollection
    {
        return $this->fileCollection;
    }

    /**
     *
     */
    public function loadFiles()
    {
        $that = $this;

        $fileCollection = $this->fileLoader->getIterator(
            $this->configuration->getArchiveBaseDirectory()
        );

        $this->fileCollection = $fileCollection->filter(function (FileInfo $fileInfo) use ($that) {
            return $that->isValidFile($fileInfo);
        });
    }

    /**
     * @param \SplFileInfo $file
     * @return bool
     */
    private function isValidFile(\SplFileInfo $file): bool
    {
        return $file->isFile();
    }
}
