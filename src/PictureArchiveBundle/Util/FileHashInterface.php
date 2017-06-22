<?php

namespace PictureArchiveBundle\Util;

interface FileHashInterface
{
    /**
     * @param $filepath
     * @return string
     */
    public function hash($filepath);
}
