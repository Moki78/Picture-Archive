<?php

namespace PictureArchiveBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 *
 * @author Moki <picture-archive@mokis-welt.de>
 */
class ImportInitializeEvent extends Event implements EventInterface
{
    const STATUS = 'initialize';

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
