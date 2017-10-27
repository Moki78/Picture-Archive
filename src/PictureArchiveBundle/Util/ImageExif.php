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
        $result = exec(
            sprintf('%s -T -createdate -modifydate -gpsdatestamp -moki -d "%%s" "%s"', $this->command, $importFilepath),
            $output,
            $status
        );

        if (0 === $status) {
            $items = explode("\t", $result);

            return $this->getDate($items);
        }

        return null;
    }

    /**
     * @param array $items
     * @return null|\DateTime
     */
    private function getDate(array $items): ?\DateTime
    {
        foreach ($items as $item) {
            try {
                if (is_numeric($item) && 0 < (int)$item) {
                    $date = new \DateTime();
                    if ($date->setTimestamp($item)) {
                        return $date;
                    }
                } else {
                    $item = preg_replace('/^(\d{4}):(\d{2}):(\d{2}).*/', '$1-$2-$3', $item);

                    return new \DateTime($item);
                }

            } catch (\Exception $e) {
                // do nothing
            }
        }

        return null;
    }
}
