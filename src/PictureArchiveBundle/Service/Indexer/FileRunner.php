<?php

namespace PictureArchiveBundle\Service\Indexer;

use PictureArchiveBundle\Service\FileRunnerAbstract;

/**
 *
 * @package PictureArchiveBundle\Import
 * @author Moki <picture-archive@mokis-welt.de>
 */
class FileRunner extends FileRunnerAbstract
{
    /**
     *
     */
    public function loadFiles()
    {
        $this->fileCollection = $this->fileLoader->getIterator(
            $this->configuration->getArchiveBaseDirectory()
        );
    }
}
