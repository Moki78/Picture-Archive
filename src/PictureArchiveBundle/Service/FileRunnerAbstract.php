<?php
/**
 * Created by PhpStorm.
 * User: moki
 * Date: 20.10.17
 * Time: 23:01
 */

namespace PictureArchiveBundle\Service;


use Doctrine\Common\Collections\ArrayCollection;
use PictureArchiveBundle\Component\Configuration;
use PictureArchiveBundle\Component\ExtendFileInfo;
use PictureArchiveBundle\Component\FileInfo;
use PictureArchiveBundle\Component\FileSystem\LoaderInterface;

/**
 *
 * @package PictureArchiveBundle\Service
 * @author Moki <picture-archive@mokis-welt.de>
 */
abstract class FileRunnerAbstract implements \Countable, \Iterator
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var \ArrayIterator
     */
    protected $fileCollection;

    /**
     * @var LoaderInterface
     */
    protected $fileLoader;

    /**
     * @var ExtendFileInfo
     */
    protected $extendFileInfo;

    /**
     * FileRunner constructor.
     *
     * @param Configuration $configuration
     * @param LoaderInterface $fileLoader
     * @param ExtendFileInfo $extendFileInfo
     */
    public function __construct(Configuration $configuration, LoaderInterface $fileLoader, ExtendFileInfo $extendFileInfo)
    {
        $this->configuration = $configuration;
        $this->fileLoader = $fileLoader;
        $this->extendFileInfo = $extendFileInfo;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->fileCollection->count();
    }

    /**
     * @return FileInfo
     */
    public function current(): FileInfo
    {
        /** @var FileInfo $current */
        $current = $this->fileCollection->current();

        $this->extendFileInfo->extend($current);

        $current->setFileType($this->getType($current));

        return $current;
    }

    /**
     * @param FileInfo $fileInfo
     * @return string
     */
    protected function getType(FileInfo $fileInfo): string
    {
        /**
         * @var string $fileType
         * @var array $types
         */
        foreach ($this->configuration->getSupportedTypes() as $fileType => $types) {
            foreach ($types as $type) {
                if (false !== strpos($fileInfo->getMimeType(), $type)) {
                    return $fileType;
                }
                if ($this->isRegularExpression($type) && preg_match($type, $fileInfo->getMimeType())) {
                    return $fileType;
                }
            }
        }
        return FileInfo::TYPE_UNKNOWN;
    }

    /**
     * @param $string
     * @return bool
     */
    protected function isRegularExpression($string): bool
    {
        set_error_handler(function () {
        }, E_WARNING);
        $isRegularExpression = FALSE !== preg_match($string, '');
        restore_error_handler();
        return $isRegularExpression;
    }

    /**
     *
     */
    public function next()
    {
        $this->fileCollection->next();
    }

    /**
     * @return int
     */
    public function key(): int
    {
        return $this->fileCollection->key();
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return $this->fileCollection->valid();
    }

    /**
     *
     */
    public function rewind()
    {
        $this->fileCollection->rewind();
    }

    /**
     * @return ArrayCollection|FileInfo[]
     */
    public function getFileCollection(): ArrayCollection
    {
        return new ArrayCollection($this->fileCollection);
    }

    /**
     *
     */
    abstract public function loadFiles();
}
