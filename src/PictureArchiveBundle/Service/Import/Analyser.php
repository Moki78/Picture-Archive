<?php

namespace PictureArchiveBundle\Service\Import;

use PictureArchiveBundle\Component\FileInfo;

/**
 *
 * @package PictureArchiveBundle\Service\Import
 * @author Moki <picture-archive@mokis-welt.de>
 */
class Analyser
{
    /**
     * @var AnalyserInterface[]
     */
    private $analyserCollection = [];

    /**
     * @param AnalyserInterface $analyser
     * @return $this
     */
    public function addAnalyser(AnalyserInterface $analyser)
    {
        $this->analyserCollection[] = $analyser;

        return $this;
    }

    /**
     * @param FileInfo $file
     * @return FileInfo
     */
    public function analyse(FileInfo $file)
    {
        foreach ($this->analyserCollection as $analyser) {
            if (!$analyser->analyse($file)) {
                return $file;
            }
        }

        $file->setStatus(FileInfo::STATUS_VALID);
        return $file;
    }
}
