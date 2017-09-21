<?php

namespace PictureArchiveBundle\Component\FileSystem;

use Doctrine\Common\Collections\ArrayCollection;
use finfo;
use PictureArchiveBundle\Component\FileInfo;
use PictureArchiveBundle\Util\FileHashInterface;

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
     * FlatLoader constructor.
     * @param FileHashInterface $hashService
     */
    public function __construct(FileHashInterface $hashService)
    {
        $this->finfo = new finfo(FILEINFO_MIME); // return mime type ala mimetype extension
        $this->hashService = $hashService;
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

        return $file;
    }
}
