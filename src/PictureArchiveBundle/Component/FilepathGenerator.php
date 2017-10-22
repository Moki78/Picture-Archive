<?php

namespace PictureArchiveBundle\Component;

/**
 *
 * @package PictureArchiveBundle\Component
 * @author Moki <picture-archive@mokis-welt.de>
 */
class FilepathGenerator
{
    /**
     * @param FileInfo $fileInfo
     * @return string
     */
    public function generate(FileInfo $fileInfo): string
    {
        return sprintf(
            '%s/%s',
            $fileInfo->getMediaDate()->format('Y/m'),
            $fileInfo->getFilename()
        );
    }
}
