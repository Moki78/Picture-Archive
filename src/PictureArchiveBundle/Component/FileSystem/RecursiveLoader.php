<?php

namespace PictureArchiveBundle\Component\FileSystem;

use Doctrine\Common\Collections\ArrayCollection;
use PictureArchiveBundle\Component\FileInfo;

/**
 *
 * @package PictureArchiveBundle\Component
 * @author Moki <picture-archive@mokis-welt.de>
 */
class RecursiveLoader extends LoaderAbstract
{
    /**
     * @param string $directoryPath
     * @return ArrayCollection
     */
    public function getIterator(string $directoryPath): ArrayCollection
    {
        $fileList = [];

        $directory = new \RecursiveDirectoryIterator($directoryPath, \RecursiveDirectoryIterator::SKIP_DOTS);

        foreach (new \RecursiveIteratorIterator($directory) as $file) {
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
