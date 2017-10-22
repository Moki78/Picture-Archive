<?php

namespace PictureArchiveBundle\Service\Import;

use PictureArchiveBundle\Component\FileInfo;
use PictureArchiveBundle\Service\FileRunnerAbstract;

/**
 *
 * @package PictureArchiveBundle\Import
 * @author Moki <picture-archive@mokis-welt.de>
 */
class FileRunner extends FileRunnerAbstract
{
    /**
     * @var array
     */
    private $excludeList = array();

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
     *
     */
    public function loadFiles()
    {
        $that = $this;

        $fileCollection = $this->fileLoader->getIterator(
            $this->configuration->getImportBaseDirectory()
        );

        $fileCollection = array_filter($fileCollection->getArrayCopy(), function (FileInfo $fileInfo) use ($that) {
            return $that->isValidFile($fileInfo);
        });

        $this->fileCollection = new \ArrayIterator($fileCollection);
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
