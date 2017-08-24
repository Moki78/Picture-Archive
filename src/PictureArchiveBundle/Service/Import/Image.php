<?php

namespace PictureArchiveBundle\Service\Import;

use PictureArchiveBundle\Entity\ImportFile;
use PictureArchiveBundle\Util\ImageExif;

/**
 *
 * @package PictureArchiveBundle\Service\Import
 * @author Moki <picture-archive@mokis-welt.de>
 */
class Image
{
    /**
     * @var ImageExif
     */
    private $imageExifService;

    /**
     * Image constructor.
     * @param ImageExif $imageExifService
     */
    public function __construct(ImageExif $imageExifService)
    {
        $this->imageExifService = $imageExifService;
    }

    /**
     * @param ImportFile $importFile
     * @return ImportFile
     */
    public function getImageDate(ImportFile $importFile)
    {
        $createDate = $this->imageExifService->getCreationDate($importFile->getFile()->getPathname());
        if ($createDate) {
            $importFile->setMediaDate($createDate);
        }

        return $importFile;
    }
}
