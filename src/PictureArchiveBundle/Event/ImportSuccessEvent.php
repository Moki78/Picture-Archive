<?php

namespace PictureArchiveBundle\Event;

use PictureArchiveBundle\Component\FileInfo;
use PictureArchiveBundle\Entity\MediaFile;

/**
 *
 * @author Moki <picture-archive@mokis-welt.de>
 */
class ImportSuccessEvent extends ImportFileEvent
{
    const STATUS = 'success';

    /**
     * @var MediaFile
     */
    private $mediaFile;

    /**
     * ImportSuccessEvent constructor.
     * @param FileInfo $fileInfo
     * @param MediaFile $mediaFile
     */
    public function __construct(FileInfo $fileInfo, MediaFile $mediaFile)
    {
        parent::__construct($fileInfo);
        $this->setMediaFile($mediaFile);
    }

    /**
     * @return MediaFile
     */
    public function getMediaFile(): MediaFile
    {
        return $this->mediaFile;
    }

    /**
     * @param MediaFile $mediaFile
     * @return ImportSuccessEvent
     */
    public function setMediaFile(MediaFile $mediaFile): ImportSuccessEvent
    {
        $this->mediaFile = $mediaFile;
        return $this;
    }
}
