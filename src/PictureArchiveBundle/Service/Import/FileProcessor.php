<?php

namespace PictureArchiveBundle\Service\Import;

use PictureArchiveBundle\Component\Configuration;
use PictureArchiveBundle\Entity\MediaFile;

class FileProcessor
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * FileProcessor constructor.
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     *
     * @throws \RuntimeException
     */
    public function initialize()
    {
        if (!is_dir($this->configuration->getArchiveBaseDirectory()) &&
            !mkdir($this->configuration->getArchiveBaseDirectory(), 0775, true)
        ) {
            throw new \RuntimeException(
                "could not create directory '{$this->configuration->getArchiveBaseDirectory()}'"
            );
        }
        if (!is_writable($this->configuration->getArchiveBaseDirectory()) ||
            !is_executable($this->configuration->getArchiveBaseDirectory())
        ) {
            throw new \RuntimeException(
                "could not write into directory '{$this->configuration->getArchiveBaseDirectory()}'"
            );
        }
    }

    /**
     * @param MediaFile $mediaFile
     * @param string $sourcepath
     * @return bool
     * @throws \RuntimeException
     */
    public function saveFile(MediaFile $mediaFile, string $sourcepath): bool
    {
        $savepath = $this->getSavePath($mediaFile);

        $saveDirectory = dirname($savepath);

        if (!is_dir($saveDirectory) && !mkdir($saveDirectory, 0775, true)) {
            throw new \RuntimeException("could not create save directory '{$saveDirectory}'");
        }

//        return copy($sourcepath, $savepath);
        return rename($sourcepath, $savepath);
    }

    /**
     * @param MediaFile $mediaFile
     * @return string
     */
    private function getSavePath(MediaFile $mediaFile): string
    {
        return $this->configuration->getArchiveBaseDirectory() . '/' . $mediaFile->getPath();
    }
}
