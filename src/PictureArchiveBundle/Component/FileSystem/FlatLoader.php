<?php

namespace PictureArchiveBundle\Component\FileSystem;

use Doctrine\Common\Collections\ArrayCollection;
use PictureArchiveBundle\Component\FileInfo;

/**
 *
 * @package PictureArchiveBundle\Component
 * @author Moki <picture-archive@mokis-welt.de>
 */
class FlatLoader extends LoaderAbstract
{
    /**
     * @param string $directory
     * @return ArrayCollection
     */
    public function getIterator(string $directory): ArrayCollection
    {
        $fileList = [];

        foreach (new \DirectoryIterator($directory) as $file) {
            /** @var FileInfo $fileInfo */
            $fileInfo = $file->getFileInfo(FileInfo::class);
            if ($this->isValidFile($fileInfo)) {
                $fileList[] = $this->extendFileInfo($fileInfo);
            }
        }

        return new ArrayCollection($fileList);
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

        return true;
    }
}
