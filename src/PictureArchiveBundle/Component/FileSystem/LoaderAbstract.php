<?php

namespace PictureArchiveBundle\Component\FileSystem;

use Doctrine\Common\Collections\ArrayCollection;
use finfo;
use PictureArchiveBundle\Component\FileInfo;
use PictureArchiveBundle\Util\FileHashInterface;
use PictureArchiveBundle\Util\ImageExif;

/**
 *
 * @package PictureArchiveBundle\Component\FileSystem
 * @author Moki <picture-archive@mokis-welt.de>
 */
abstract class LoaderAbstract
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
     * @param string $directory
     * @return ArrayCollection
     */
    abstract public function getIterator(string $directory): ArrayCollection;

    /**
     * @param FileInfo $file
     * @return FileInfo
     */
    protected function extendFileInfo(FileInfo $file): FileInfo
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
