<?php

namespace PictureArchiveBundle\Component\FileSystem;

/**
 *
 * @package PictureArchiveBundle\Component\FileSystem
 * @author Moki <picture-archive@mokis-welt.de>
 */
interface LoaderInterface
{
    /**
     * @param string $directory
     * @return \ArrayIterator
     */
    public function getIterator(string $directory): \ArrayIterator;
}
