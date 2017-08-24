<?php

namespace PictureArchiveBundle\Service\Import;

use PictureArchiveBundle\Entity\ImportFile;

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
     * @param ImportFile $file
     * @return ImportFile
     */
    public function analyse(ImportFile $file)
    {
        foreach ($this->analyserCollection as $analyser) {
            if (!$analyser->analyse($file)) {
                $file->setStatus(ImportFile::STATUS_INVALID);
                return $file;
            }
        }

        $file->setStatus(ImportFile::STATUS_VALID);
        return $file;
    }
}
