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
    protected function configure(): void
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
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \LogicException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $progress = new ProgressBar($output);
        $progress->setFormat('debug');

        $processor = $this->getContainer()->get('picture_archive.import');
        $processor->run($progress);

        $output->writeln('');
        $output->writeln('');
        $output->writeln('import done');
    }
}
