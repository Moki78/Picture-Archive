<?php

namespace PictureArchiveBundle\Util;

/**
 * Class ImageExif
 * @package PictureArchiveBundle\Util
 */
class ImageExif
{
    private $command;

    /**
     * ImageExif constructor.
     * @param string $command
     */
    public function __construct($command)
    {
        $this->command = $command;
    }

    /**
     * @param string $importFilepath
     * @return \DateTime|null
     */
    public function getCreationDate($importFilepath): ?\DateTime
    {
        $time = exec(
            sprintf('%s -T -createdate -d "%%s" "%s"', $this->command, $importFilepath),
            $output,
            $status
        );

        if ($status === 0 && $time != '-') {
            if ($time > 0) {
                $date = new \DateTime();
                $date->setTimestamp($time);

                return $date;
            }
        }
        return null;
    }
}
