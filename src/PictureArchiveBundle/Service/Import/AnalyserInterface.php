<?php

namespace PictureArchiveBundle\Service\Import;

use PictureArchiveBundle\Component\FileInfo;

/**
 * @package PictureArchiveBundle\Service\Import\Analyser
 * @author Moki <picture-archive@mokis-welt.de>
 */
interface AnalyserInterface
{
    /**
     * @param FileInfo $importFile
     * @return bool
     */
    public function analyse(FileInfo $importFile): bool;
}
