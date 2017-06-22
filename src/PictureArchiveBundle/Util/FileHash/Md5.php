<?php

namespace PictureArchiveBundle\Util\FileHash;

use PictureArchiveBundle\Util\FileHashInterface;

class Md5 implements FileHashInterface
{
    /**
     * @param $filepath
     * @return string
     */
    public function hash($filepath)
    {
        return md5_file($filepath);
    }

}
