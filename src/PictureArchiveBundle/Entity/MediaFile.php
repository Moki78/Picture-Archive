<?php

namespace PictureArchiveBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @package PictureArchiveBundle\Entity
 * @author Moki <picture-archive@mokis-welt.de>
 *
 * @ORM\Table(name="files")
 * @ORM\Entity(repositoryClass="PictureArchiveBundle\Repository\FilesRepository")
 * @ORM\HasLifecycleCallbacks
 */
class MediaFile
{
    const STATUS_NEW = 1;
    const STATUS_IMPORTED = 2;
    const STATUS_CONFLICT = 4;

    const FILE_IMAGE = 1;
    const FILE_VIDEO = 2;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer", nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="string", length=32)
     */
    private $hash;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, unique=true)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="mime_type", type="string", length=255, nullable=true)
     */
    private $mimeType;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", options={"default":0})
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="media_date", type="datetime")
     */
    private $mediaDate;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $type
     * @return MediaFile
     */
    public function setType(int $type): MediaFile
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * Set hash
     *
     * @param string $hash
     *
     * @return MediaFile
     */
    public function setHash($hash): MediaFile
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * Set filepath
     *
     * @param string $path
     *
     * @return MediaFile
     */
    public function setPath($path): MediaFile
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get filepath
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return MediaFile
     */
    public function setCreatedAt($createdAt): MediaFile
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set mediaDate
     *
     * @param \DateTime $date
     *
     * @return MediaFile
     */
    public function setMediaDate($date): MediaFile
    {
        $this->mediaDate = $date;

        return $this;
    }

    /**
     * Get mediaDate
     *
     * @return \DateTime
     */
    public function getMediaDate(): \DateTime
    {
        return $this->mediaDate;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return MediaFile
     */
    public function setUpdatedAt($updatedAt): MediaFile
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * Set filename
     *
     * @param string $name
     *
     * @return MediaFile
     */
    public function setName($name): MediaFile
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set mimeType
     *
     * @param string $mimeType
     *
     * @return MediaFile
     */
    public function setMimeType($mimeType): MediaFile
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Get mimeType
     *
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * Set mimeType
     *
     * @param string $status
     *
     * @return MediaFile
     */
    public function setStatus($status): MediaFile
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get mimeType
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setTimestamp(): void
    {
        if (null === $this->getCreatedAt()) {
            $this->setCreatedAt(new \DateTime('now'));
        }
        $this->setUpdatedAt(new \DateTime('now'));
    }
}
