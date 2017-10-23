<?php

namespace PictureArchiveBundle\Command;

use PictureArchiveBundle\Component\Report;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 * @package PictureArchiveBundle\Command
 * @author Moki <picture-archive@mokis-welt.de>
 */
class ImportCommand extends ContainerAwareCommand
{
    protected function configure(): void
    {
        $this
            ->setName('picturearchive:import')
            ->setDescription('import cron script')

            ->addOption('progress', 'p', InputOption::VALUE_NONE, 'show progressbar')
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'import limit (default: 100)', 100)
            ->addOption('report-directory', null, InputOption::VALUE_OPTIONAL, 'report directory (default: php://stdout)')
            ->addOption(
                'report-text',
                null,
                InputOption::VALUE_NONE,
                'import reporter text')
            ->addOption(
                'report-csv',
                null,
                InputOption::VALUE_NONE,
                'import reporter csv'

            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $configuration = $this->getContainer()->get('picture_archive.component.configuration');

        // reporter
        if ($directory = $input->getOption('report-directory')) {
            $this->checkDirectory($directory);
        }

        if ($input->getOption('report-text')) {
            $configuration->addReporter(new Report\Text($directory));
        }

        if ($input->getOption('report-csv')) {
            $configuration->addReporter(new Report\Csv($directory));
        }

        $configuration->setImportFileLimit((int)$input->getOption('limit'));
    }

    /**
     * @param $directory
     * @return bool
     * @throws \InvalidArgumentException
     */
    private function checkDirectory($directory): bool
    {
        $realpath = realpath($directory);

        if (file_exists($realpath) && is_dir($realpath)) {
            if (is_writable($realpath)) {
                return true;
            }

            throw new \InvalidArgumentException("file '{$realpath}' is not writable");
        }

        if (is_file($realpath)) {
            throw new \InvalidArgumentException("file '{$realpath}' is a file, must be a directory");
        }

        if (!mkdir($directory)) {
            throw new \InvalidArgumentException("could not create directory'{$directory}'");
        }

        return true;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \LogicException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $progressOutput = $output;
        if (!$input->getOption('progress')) {
            $progressOutput = clone $output;
            $progressOutput->setVerbosity(OutputInterface::VERBOSITY_QUIET);
        }

        $progress = new ProgressBar($progressOutput);
        $progress->setFormat('debug');


        $processor = $this->getContainer()->get('picture_archive.import');
        $processor->run($progress);

        $output->writeln('');
        $output->writeln('');
        $output->writeln('import done');
    }
}
