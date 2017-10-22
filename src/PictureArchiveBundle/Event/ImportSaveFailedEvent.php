<?php

namespace PictureArchiveBundle\Event;

use PictureArchiveBundle\Component\FileInfo;
use PictureArchiveBundle\Entity\MediaFile;

/**
 *
 * @author Moki <picture-archive@mokis-welt.de>
 */
class ImportSaveFailedEvent extends ImportFileEvent
{
    const STATUS = 'save failed';
    /**
     * @var string
     */
    protected $message;
    /**
     * @var MediaFile
     */
    private $mediaFile;

    /**
     * ImportFailedEvent constructor.
     * @param FileInfo $fileInfo
     * @param MediaFile $mediaFile
     * @param string $message
     */
    public function __construct(FileInfo $fileInfo, MediaFile $mediaFile, string $message)
    {
        parent::__construct($fileInfo);

        $this->setMessage($message);
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
     * @return ImportSaveFailedEvent
     */
    public function setMediaFile(MediaFile $mediaFile): ImportSaveFailedEvent
    {
        $this->mediaFile = $mediaFile;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return ImportSaveFailedEvent
     */
    public function setMessage(string $message): ImportSaveFailedEvent
    {
        $this->message = $message;
        return $this;
    }
}
