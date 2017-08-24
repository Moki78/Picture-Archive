<?php

namespace PictureArchiveBundle\Entity;

/**
 *
 * @package PictureArchiveBundle\Entity
 * @author Moki <picture-archive@mokis-welt.de>
 */
class ImportFile
{
    const TYPE_UNKNOWN = 0;
    const TYPE_IMAGE = 1;
    const TYPE_VIDEO = 2;

    const STATUS_VALID = 1;
    const STATUS_INVALID = 2;

    /**
     * @var \SplFileInfo
     */
    private $file;

    /**
     * @var \DateTime
     */
    private $fileDate;

    /**
     * @var \DateTime
     */
    private $mediaDate;

    /**
     * @var string
     */
    private $fileType;

    /**
     * @var string
     */
    private $mimeType;

    /**
     * @var int
     */
    private $status;

    /**
     * @var string
     */
    private $statusMessage = '';

    /**
     * @return \SplFileInfo
     */
    public function getFile(): \SplFileInfo
    {
        return $this->file;
    }

    /**
     * @param \SplFileInfo $file
     * @return ImportFile
     */
    public function setFile(\SplFileInfo $file): ImportFile
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @return string
     */
    public function getFileType(): string
    {
        return $this->fileType;
    }

    /**
     * @param string $fileType
     * @return ImportFile
     */
    public function setFileType(string $fileType): ImportFile
    {
        $this->fileType = $fileType;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFileDate(): \DateTime
    {
        return $this->fileDate;
    }

    /**
     * @param \DateTime $fileDate
     * @return ImportFile
     */
    public function setFileDate(\DateTime $fileDate): ImportFile
    {
        $this->fileDate = $fileDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getMediaDate(): \DateTime
    {
        return $this->mediaDate;
    }

    /**
     * @param \DateTime $mediaDate
     * @return ImportFile
     */
    public function setMediaDate(\DateTime $mediaDate): ImportFile
    {
        $this->mediaDate = $mediaDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     * @return ImportFile
     */
    public function setMimeType(string $mimeType): ImportFile
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return ImportFile
     */
    public function setStatus(int $status): ImportFile
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatusMessage(): string
    {
        return $this->statusMessage;
    }

    /**
     * @param string $statusMessage
     * @return ImportFile
     */
    public function setStatusMessage(string $statusMessage): ImportFile
    {
        $this->statusMessage = $statusMessage;
        return $this;
    }
}
