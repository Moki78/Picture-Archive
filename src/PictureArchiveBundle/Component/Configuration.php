<?php

namespace PictureArchiveBundle\Component;

use PictureArchiveBundle\Component\Report\ReportInterface;

/**
 *
 * @package PictureArchiveBundle\Component
 * @author Moki <picture-archive@mokis-welt.de>
 */
class Configuration
{
    /**
     * @var string
     */
    private $archiveBaseDirectory;

    /**
     *
     * @var string
     */
    private $importBaseDirectory;

    /**
     * @var string
     */
    private $importFailedDirectory;

    /**
     * @var array
     */
    private $supportedTypes = [];

    /**
     * @var int
     */
    private $importMinimumFileAge;

    /**
     * @var array
     */
    private $tools = [];


    private $reporter = [];

    /**
     * @return string
     */
    public function getArchiveBaseDirectory(): string
    {
        return $this->archiveBaseDirectory;
    }

    /**
     * @param string $archiveBaseDirectory
     * @return Configuration
     */
    public function setArchiveBaseDirectory(string $archiveBaseDirectory): Configuration
    {
        $this->archiveBaseDirectory = $archiveBaseDirectory;
        return $this;
    }

    /**
     * @return string
     */
    public function getImportBaseDirectory(): string
    {
        return $this->importBaseDirectory;
    }

    /**
     * @param string $importBaseDirectory
     * @return Configuration
     */
    public function setImportBaseDirectory(string $importBaseDirectory): Configuration
    {
        $this->importBaseDirectory = $importBaseDirectory;
        return $this;
    }

    /**
     * @return string
     */
    public function getImportFailedDirectory(): string
    {
        return $this->importFailedDirectory;
    }

    /**
     * @param string $importFailedDirectory
     * @return Configuration
     */
    public function setImportFailedDirectory(string $importFailedDirectory): Configuration
    {
        $this->importFailedDirectory = $importFailedDirectory;
        return $this;
    }

    /**
     * @return array
     */
    public function getSupportedTypes(): array
    {
        return $this->supportedTypes;
    }

    /**
     * @param array $supportedTypes
     * @return Configuration
     */
    public function setSupportedTypes(array $supportedTypes): Configuration
    {
        $this->supportedTypes = $supportedTypes;
        return $this;
    }

    /**
     * @return int
     */
    public function getImportMinimumFileAge(): int
    {
        return $this->importMinimumFileAge;
    }

    /**
     * @param int $importMinimumFileAge
     * @return Configuration
     */
    public function setImportMinimumFileAge(int $importMinimumFileAge): Configuration
    {
        $this->importMinimumFileAge = $importMinimumFileAge;
        return $this;
    }

    /**
     * @return ReportInterface[]
     */
    public function getReporter(): array
    {
        return $this->reporter;
    }

    /**
     * @param ReportInterface[] $reporter
     * @return Configuration
     */
    public function setReporter(array $reporter): Configuration
    {
        $this->reporter = $reporter;
        return $this;
    }

    /**
     * @param ReportInterface $reporter
     * @return Configuration
     */
    public function addReporter(ReportInterface $reporter): Configuration
    {
        $this->reporter[] = $reporter;
        return $this;
    }

    /**
     * @return array
     */
    public function getTools(): array
    {
        return $this->tools;
    }

    /**
     * @param array $tools
     * @return Configuration
     */
    public function setTools(array $tools): Configuration
    {
        $this->tools = $tools;
        return $this;
    }


}
