<?php

namespace PictureArchiveBundle\Event;

use PictureArchiveBundle\Component\FileInfo;

/**
 *
 * @author Moki <picture-archive@mokis-welt.de>
 */
class ImportAnalysisFailedEvent extends ImportFileEvent
{
    const STATUS = 'analysis failed';
    /**
     * @var string
     */
    protected $message;

    /**
     * ImportFailedEvent constructor.
     * @param FileInfo $fileInfo
     * @param string $message
     */
    public function __construct(FileInfo $fileInfo, string $message)
    {
        parent::__construct($fileInfo);

        $this->setMessage($message);
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
    public function setMessage(string $message): ImportAnalysisFailedEvent
    {
        $this->message = $message;
        return $this;
    }
}
