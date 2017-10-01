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
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED)
            ->addOption('progress', 'p', InputOption::VALUE_NONE, 'show progressbar')
            ->addOption(
                'reporter-stdout',
                null,
                InputOption::VALUE_NONE,
                'import reporter stdout (default)')
            ->addOption(
                'reporter-csv',
                null,
                InputOption::VALUE_OPTIONAL,
                'import reporter csv, if no option value, stdout is used'

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
        if (false !== $input->getOption('reporter-csv')) {
            $filepath = $input->getOption('reporter-csv');
            if ($filepath) {
                $this->checkFile($filepath);
            } else {
                $filepath = 'php://stdout';
            }

            $configuration->addReporter(new Report\Csv($filepath));
        }

        if ($input->getOption('reporter-stdout') || 0 === count($configuration->getReporter())) {
            $configuration->addReporter(new Report\Stdout());
        }
    }

    /**
     * @param $filepath
     * @return bool
     * @throws \InvalidArgumentException
     */
    private function checkFile($filepath): bool
    {
        $realpath = realpath($filepath);

        if (file_exists($realpath) && !is_dir($realpath)) {
            if (is_writable($realpath)) {
                return true;
            }

            throw new \InvalidArgumentException("file '{$realpath}' is not writable");
        }

        if (is_dir($realpath)) {
            throw new \InvalidArgumentException("file '{$realpath}' is a dirctory");
        }

        $directorypath = dirname($filepath);
        if (!is_dir($directorypath)) {
            throw new \InvalidArgumentException("save directory '{$directorypath}' is not a dirctory");
        }

        if (!is_writable($directorypath)) {
            throw new \InvalidArgumentException("save directory '{$directorypath}' is not writeable");
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
