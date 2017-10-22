<?php

namespace PictureArchiveBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 *
 * @author Moki <picture-archive@mokis-welt.de>
 */
class ImportFinishEvent extends Event implements EventInterface
{
    const STATUS = 'finish';

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return static::STATUS;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return '';
    }
}
