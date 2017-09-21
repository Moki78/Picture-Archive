<?php

namespace PictureArchiveBundle\Service\Import\Analyser;

use PictureArchiveBundle\Component\FileInfo;
use PictureArchiveBundle\Service\Import\AnalyserInterface;

/**
 *
 * @package PictureArchiveBundle\Service\Import
 * @author Moki <picture-archive@mokis-welt.de>
 */
class MediaDateIsset implements AnalyserInterface
{
    /**
     * @param FileInfo $fileInfo
     * @return bool
     */
    public function analyse(FileInfo $fileInfo): bool
    {
        if ($fileInfo->getMediaDate() instanceof \DateTime) {
            $fileInfo->setStatus(FileInfo::STATUS_VALID);
            return true;
        }

        $fileInfo->setStatus(FileInfo::STATUS_INVALID);
        $fileInfo->setStatusMessage('no valid media date found');

        return false;
    }
}
