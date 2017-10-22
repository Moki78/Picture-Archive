<?php

namespace PictureArchiveBundle\Event;

/**
 *
 * @author Moki <picture-archive@mokis-welt.de>
 */
interface EventInterface
{
    /**
     * @return string
     */
    public function getStatus(): string;

    /**
     * @return string
     */
    public function getMessage(): string;
}
