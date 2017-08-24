<?php

namespace PictureArchiveBundle\Util;

use Doctrine\Common\Collections\ArrayCollection;
use PictureArchiveBundle\Import\ImportFile;
use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;

class FileScanner
{
    /**
     * @var array|\DirectoryIterator[]
     */
    private $directory = array();

    /**
     * @var ArrayCollection|ImportFile[]
     */
    private $fileList;

    /**
     * @var array
     */
    private $excludeList = array();

    /**
     * @param \DirectoryIterator $directory
     * @return $this
     */
    public function setDirectory(\DirectoryIterator $directory)
    {
        $this->directory[] = $directory;

        return $this;
    }

    /**
     * @param array $excludeList
     * @return $this
     */
    public function setExcludeList(array $excludeList)
    {
        $this->excludeList = $excludeList;

        return $this;
    }

    /**
     * @param $exclude
     * @return $this
     */
    public function addExcludeList($exclude)
    {
        $this->excludeList[] = $exclude;

        return $this;
    }



    /**
     * @return \ArrayObject|ImportFile[]
     * @throws \InvalidArgumentException
     */
    public function getFiles()
    {
        $this->fileList = new ArrayCollection();

        foreach ($this->directory as $directory) {
            if ($directory instanceof \RecursiveDirectoryIterator) {
                $iterator = new \RecursiveIteratorIterator($directory);
            } elseif ($directory instanceof \DirectoryIterator) {
                $iterator = new \IteratorIterator($directory);
            } else {
                throw new \InvalidArgumentException('invalid iterator type');
            }

            $this->loadFiles($iterator);
        }

        return $this->fileList;
    }



    /**
     * @param \Iterator $iterator
     */
    private function loadFiles(\Iterator $iterator)
    {
        foreach ($iterator as $item) {
            $this->addFile($item);
        }
    }

    /**
     * @param \SplFileInfo $file
     */
    private function addFile(\SplFileInfo $file)
    {
        if ($file->isDir() || $file->isLink()) {
            return;
        }

        foreach ($this->excludeList as $excludeItem) {
            if (false !== strpos($file->getPathname(), $excludeItem)) {
                return;
            }

        }

        $importFile = new ImportFile();
        $importFile->setCreateDate(new \DateTime());
        $importFile->getCreateDate()->setTimestamp($file->getMTime());
        $importFile
            ->setMimeType($this->getMime($file->getPathname()))
            ->setFilepath($file->getPathname())
            ->setFilename($file->getFilename());

        $this->fileList->add($importFile);
    }

    /**
     * @param string $filepath
     * @return string
     */
    private function getMime($filepath)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filepath);
        finfo_close($finfo);

        return $mime;
    }
}
