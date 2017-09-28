<?php

namespace PictureArchiveBundle\Component;

/**
 *
 * @package PictureArchiveBundle\Component
 * @author Moki <picture-archive@mokis-welt.de>
 */
class FilepathGenerator
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * FilepathGenerator constructor.
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param FileInfo $fileInfo
     * @return string
     */
    public function generate(FileInfo $fileInfo): string
    {
        $subDirectories = $this->configuration->getArchiveFiletypeSubdirectory();

        if (array_key_exists($fileInfo->getFileType(), $subDirectories)) {
            return sprintf(
                '%s/%s/%s',
                $subDirectories[$fileInfo->getFileType()],
                $fileInfo->getMediaDate()->format('Y/m'),
                $fileInfo->getFilename()
            );
        }

        return sprintf(
            '%s/%s',
            $fileInfo->getMediaDate()->format('Y/m'),
            $fileInfo->getFilename()
        );
    }
}
