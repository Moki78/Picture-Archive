<?php

namespace PictureArchiveBundle\Event;

use PictureArchiveBundle\Component\FileInfo;
use PictureArchiveBundle\Entity\MediaFile;
use Symfony\Component\EventDispatcher\Event;

/**
 *
 * @author Moki <picture-archive@mokis-welt.de>
 */
class ImportEvent extends Event
{
    const STATUS_START = 1;
    const STATUS_FINISH = 2;
    const STATUS_ANALYSE_FAILED = 3;
    const STATUS_SAVE_FAILED = 4;
    const STATUS_SUCCESS = 5;
    const STATUS_ERROR = 6;

    /**
     * @var FileInfo
     */
    private $FileInfo;

    /**
     * @var MediaFile
     */
    private $mediaFile;

    /**
     * @var int
     */
    private $status;

    /**
     * @var string
     */
    private $message;

    /**
     * @return FileInfo
     */
    public function getFileInfo(): ?FileInfo
    {
        return $this->FileInfo;
    }

    /**
     * @param FileInfo $FileInfo
     * @return ImportEvent
     */
    public function setFileInfo(?FileInfo $FileInfo): ImportEvent
    {
        $this->FileInfo = $FileInfo;
        return $this;
    }

    /**
     * @return MediaFile
     */
    public function getMediaFile(): ?MediaFile
    {
        return $this->mediaFile;
    }

    /**
     * @param MediaFile $mediaFile
     * @return ImportEvent
     */
    public function setMediaFile(?MediaFile $mediaFile): ImportEvent
    {
        $this->mediaFile = $mediaFile;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return ImportEvent
     */
    public function setStatus(int $status): ImportEvent
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return ImportEvent
     */
    public function setMessage(string $message): ImportEvent
    {
        $this->message = $message;
        return $this;
    }
}
