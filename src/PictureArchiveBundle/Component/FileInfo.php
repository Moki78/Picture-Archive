<?php

namespace PictureArchiveBundle\Component;

/**
 *
 * @package PictureArchiveBundle\Entity
 * @author Moki <picture-archive@mokis-welt.de>
 */
class FileInfo extends \SplFileInfo
{
    const TYPE_UNKNOWN = 0;
    const TYPE_IMAGE = 1;
    const TYPE_VIDEO = 2;

    const STATUS_VALID = 1;
    const STATUS_INVALID = 2;
    const STATUS_WAIT = 3;

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
     * @var string
     */
    private $fileHash;

    /**
     * @var int
     */
    private $status;

    /**
     * @var string
     */
    private $statusMessage = '';

    /**
     * @return string
     */
    public function getFileType(): ?string
    {
        return $this->fileType;
    }

    /**
     * @param string $fileType
     * @return FileInfo
     */
    public function setFileType(string $fileType): FileInfo
    {
        $this->fileType = $fileType;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFileDate(): ?\DateTime
    {
        return $this->fileDate;
    }

    /**
     * @param \DateTime $fileDate
     * @return FileInfo
     */
    public function setFileDate(\DateTime $fileDate): FileInfo
    {
        $this->fileDate = $fileDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileHash(): ?string
    {
        return $this->fileHash;
    }

    /**
     * @param string $fileHash
     * @return FileInfo
     */
    public function setFileHash(string $fileHash): FileInfo
    {
        $this->fileHash = $fileHash;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getMediaDate(): ?\DateTime
    {
        return $this->mediaDate;
    }

    /**
     * @param \DateTime $mediaDate
     * @return FileInfo
     */
    public function setMediaDate(\DateTime $mediaDate): FileInfo
    {
        $this->mediaDate = $mediaDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     * @return FileInfo
     */
    public function setMimeType(string $mimeType): FileInfo
    {
        $this->mimeType = $mimeType;

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
     * @return FileInfo
     */
    public function setStatus(int $status): FileInfo
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatusMessage(): ?string
    {
        return $this->statusMessage;
    }

    /**
     * @param string $statusMessage
     * @return FileInfo
     */
    public function setStatusMessage(string $statusMessage): FileInfo
    {
        $this->statusMessage = $statusMessage;
        return $this;
    }
}
