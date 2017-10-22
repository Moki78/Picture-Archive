<?php

namespace PictureArchiveBundle\Component\FileSystem;

use PictureArchiveBundle\Component\FileInfo;

/**
 *
 * @package PictureArchiveBundle\Component
 * @author Moki <picture-archive@mokis-welt.de>
 */
class RecursiveLoader implements LoaderInterface
{
    /**
     * @param string $directoryPath
     * @return \ArrayIterator
     */
    public function getIterator(string $directoryPath): \ArrayIterator
    {
        $fileList = [];

        $directory = new \RecursiveDirectoryIterator($directoryPath, \RecursiveDirectoryIterator::SKIP_DOTS);

        foreach (new \RecursiveIteratorIterator($directory) as $file) {
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
