<?php

namespace PictureArchiveBundle\Component;

use finfo;
use PictureArchiveBundle\Util\FileHashInterface;
use PictureArchiveBundle\Util\ImageExif;

/**
 *
 * @package PictureArchiveBundle\Component
 * @author Moki <picture-archive@mokis-welt.de>
 */
class ExtendFileInfo
{
    /**
     * @var FileHashInterface
     */
    protected $hashService;

    /**
     * @var finfo
     */
    protected $finfo;

    /**
     * @var ImageExif
     */
    private $imageExif;

    /**
     * FlatLoader constructor.
     * @param FileHashInterface $hashService
     * @param ImageExif $imageExif
     */
    public function __construct(FileHashInterface $hashService, ImageExif $imageExif)
    {
        $this->finfo = new finfo(FILEINFO_MIME); // return mime type ala mimetype extension
        $this->hashService = $hashService;
        $this->imageExif = $imageExif;
    }

    /**
     * @param FileInfo $file
     * @return FileInfo
     */
    public function extend(FileInfo $file): FileInfo
    {
        $fileDate = new \DateTime();
        $fileDate->setTimestamp($file->getMTime());

        $file->setFileDate($fileDate);
        $file->setFileHash($this->hashService->hash($file->getPathname()));
        $file->setMimeType($this->finfo->file($file->getPathname()));
        $file->setMediaDate($this->imageExif->getCreationDate($file->getPathname()));

        return $file;
    }
}
