<?php

namespace PictureArchiveBundle\Component\FileSystem;

use PictureArchiveBundle\Component\FileInfo;

/**
 *
 * @package PictureArchiveBundle\Component
 * @author Moki <picture-archive@mokis-welt.de>
 */
class FlatLoader implements LoaderInterface
{
    /**
     * @param string $directory
     * @return \ArrayIterator
     */
    public function getIterator(string $directory): \ArrayIterator
    {
        $fileList = [];

        foreach (new \DirectoryIterator($directory) as $file) {
            /** @var FileInfo $fileInfo */
            $fileInfo = $file->getFileInfo(FileInfo::class);
            if ($this->isValidFile($fileInfo)) {
                $fileList[] = $fileInfo;
            }
        }

        return new \ArrayIterator($fileList);
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
