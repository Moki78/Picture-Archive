<?php

namespace PictureArchiveBundle\Event;

use PictureArchiveBundle\Component\FileInfo;
use Symfony\Component\EventDispatcher\Event;

/**
 *
 * @author Moki <picture-archive@mokis-welt.de>
 */
class ImportFileEvent extends Event implements EventInterface
{
    const STATUS = 'import file';

    /**
     * @var FileInfo
     */
    protected $fileInfo;

    /**
     * ImportFailedEvent constructor.
     * @param FileInfo $fileInfo
     */
    public function __construct(FileInfo $fileInfo)
    {
        $this->setFileInfo($fileInfo);
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return static::STATUS;
    }

    public function getMessage(): string
    {
        return '';
    }

    /**
     * @return FileInfo
     */
    public function getFileInfo(): FileInfo
    {
        return $this->fileInfo;
    }

    /**
     * @param FileInfo $fileInfo
     * @return ImportFileEvent
     */
    public function setFileInfo(FileInfo $fileInfo): ImportFileEvent
    {
        $this->fileInfo = $fileInfo;
        return $this;
    }
}
