<?php

namespace PictureArchiveBundle\Event;

use PictureArchiveBundle\Component\FileInfo;

/**
 *
 * @author Moki <picture-archive@mokis-welt.de>
 */
class ImportErrorEvent extends ImportFileEvent
{
    const STATUS = 'error';

    /**
     * @var string
     */
    protected $exception;

    /**
     * ImportFailedEvent constructor.
     * @param FileInfo $fileInfo
     * @param \Exception $exception
     */
    public function __construct(FileInfo $fileInfo, \Exception $exception)
    {
        parent::__construct($fileInfo);

        $this->setException($exception);
    }

    /**
     * @return string
     */
    public function getException(): string
    {
        return $this->exception;
    }

    /**
     * @param \Exception $exception
     * @return ImportErrorEvent
     */
    public function setException(\Exception $exception): ImportErrorEvent
    {
        $this->exception = $exception;
        return $this;
    }
}
