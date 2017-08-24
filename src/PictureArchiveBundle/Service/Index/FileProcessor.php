<?php

namespace PictureArchiveBundle\Service\Index;

class FileProcessor
{
    /**
     * @param string $source
     * @param string $target
     */
    public function saveFile($source, $target)
    {

        $pathname = dirname($target);

        if (!is_dir($pathname) && !mkdir($pathname, 0775, true)) {
            throw new \RuntimeException("could not create directory '{$pathname}'");
        }

        rename($source, $target);
    }

    public function saveFailedFile($importFilepath, $failDirectory)
    {
        if (!is_dir($failDirectory)) {
            if (!mkdir($failDirectory, 0775, true)) {
                throw new \RuntimeException("could not create directory '{$failDirectory}'");
            }
        }

        $path = explode("/", $importFilepath);

        $dstFilepath = rtrim($failDirectory, '/') . '/' . end($path);

        rename($importFilepath, $dstFilepath);
    }
}
