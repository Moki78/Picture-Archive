<?php

namespace PictureArchiveBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 * @package PictureArchiveBundle\Command
 * @author Moki <picture-archive@mokis-welt.de>
 */
class ImportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('picturearchive:import')
            ->setDescription('import cron script')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Symfony\Component\Console\Exception\LogicException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \LogicException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $progress = new ProgressBar($output);
        $progress->setFormat('debug');

        $importDirectory = $this->getContainer()->getParameter('picture_archive.import_directory');
        $directory = new \DirectoryIterator($importDirectory);

        $scanner = $this->getContainer()->get('picture_archive.file.scanner');
        $scanner->setDirectory($directory);
        $scanner->addExcludeList($this->getContainer()->getParameter('picture_archive.import_failed_directory'));

        $processor = $this->getContainer()->get('picture_archive.import.processor');
        $processor->run($progress);

        $output->writeln('import done');
    }
}
