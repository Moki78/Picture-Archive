<?php

namespace PictureArchiveBundle\Service\Import;

use PictureArchiveBundle\Entity\ImportFile;

/**
 * @package PictureArchiveBundle\Service\Import\Analyser
 * @author Moki <picture-archive@mokis-welt.de>
 */
interface AnalyserInterface
{
    /**
     * @param ImportFile $fileimportFile
     * @return bool
     */
    public function analyse(ImportFile $fileimportFile);
}
