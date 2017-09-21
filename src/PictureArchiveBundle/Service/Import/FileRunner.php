<?php

namespace PictureArchiveBundle\Service\Import;

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
     * @var array
     */
    private $excludeList = array();

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

    public function count()
    {
        return $this->fileCollection->count();
    }

    /**
     * @return ArrayCollection
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
            $this->configuration->getImportBaseDirectory()
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
        foreach ($this->excludeList as $excludeItem) {
            if (false !== strpos($file->getPathname(), $excludeItem)) {
                return false;
            }
        }

        return true;
    }
}
