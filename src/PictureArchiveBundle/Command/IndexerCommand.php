<?php

namespace PictureArchiveBundle\Command;

use Doctrine\Common\Collections\ArrayCollection;
use PictureArchiveBundle\Entity\MediaFile;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 *
 * @package PictureArchiveBundle\Command
 * @author Moki <picture-archive@mokis-welt.de>
 */
class IndexerCommand extends ContainerAwareCommand
{
    protected function configure(): void
    {
        $this
            ->setName('picturearchive:indexer')
            ->setDescription('Picture Archive Indexer')
            ->addOption('reindex', null, InputOption::VALUE_NONE, 'reindex all')
            ->addOption('list-duplicates', null, InputOption::VALUE_NONE, 'list files with same hashes')
            ->addOption('fix', null, InputOption::VALUE_NONE, 'fix confliced files')
            ->addOption('progress', 'p', InputOption::VALUE_NONE, 'show progressbar');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Symfony\Component\Console\Exception\LogicException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $processor = $this->getContainer()->get('picture_archive.indexer');

        if ($input->getOption('reindex')) {
            $progressOutput = $output;
            if (!$input->getOption('progress')) {
                $progressOutput = clone $output;
                $progressOutput->setVerbosity(OutputInterface::VERBOSITY_QUIET);
            }

            $progress = new ProgressBar($progressOutput);
            $progress->setFormat('debug');


            $processor = $this->getContainer()->get('picture_archive.indexer');
            $processor->run($progress);

            $output->writeln('');
            $output->writeln('');
            $output->writeln('indexer done');

            return 0;
        }

        if ($input->getOption('list-duplicates')) {
            $duplicates = $processor->getDuplicates();

            $output->writeln('found ' . $duplicates->count() . ' duplicate hashes');

            foreach ($duplicates as $hash => $items) {
                echo PHP_EOL . $hash . ':' . PHP_EOL;

                if ($input->getOption('fix')) {
                    $this->askForDeletion($input, $output, $items);
                } else {
                    $items->forAll(
                        function ($key, MediaFile $file) {
                            printf(
                                '    %s - %s' . PHP_EOL,
                                $file->getMediaDate() ? $file->getMediaDate()->format('Y-m-d H:i:s') : '',
                                $file->getPath()
                            );
                            return true;
                        }
                    );
                }
            }

            return 0;
        }

        $this->showStats($output);
        return 0;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param ArrayCollection|MediaFile[] $items
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \LogicException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Console\Exception\LogicException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function askForDeletion(
        InputInterface $input,
        OutputInterface $output,
        ArrayCollection $items
    ): void
    {
        $choices = array();
        $index = 0;


        foreach ($items as $file) {
            $choices[++$index] = $file->getPath();
        }
        $choices['i'] = 'ignore, go to next';


        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'which files should be deleted?',
            $choices,
            'i'
        );
        $question->setMultiselect(true);

        /** @var array $selected */
        $selected = $helper->ask($input, $output, $question);
        if (in_array('i', $selected, true)) {
            $output->writeln('hash ignored, do nothing');
            return;
        }

        if (count($selected) === $items->count()) {
            $output->writeln('could not delete all files');
            return;
        }

        $indexer = $this->getContainer()->get('picture_archive.indexer');

        foreach ($selected as $index) {
            $filepath = $choices[$index];
            foreach ($items as $file) {
                if ($filepath === $file->getPath()) {
                    $output->writeln('remove file ' . $file->getPath());

                    if ($indexer->deleteConflictedFile($file)) {
                        $items->removeElement($file);

                        $output->writeln('file removed');
                    } else {
                        $output->writeln('could not remove file');
                    }
                }
            }
        }
        if (1 === $items->count()) {
            $items->first()->setStatus(MediaFile::STATUS_IMPORTED);
            $output->writeln('the hash is not conflicted anymore');
        }

        $this->getContainer()->get('doctrine.orm.entity_manager')->flush();
    }

    /**
     * @param OutputInterface $output
     * @throws \LogicException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function showStats(OutputInterface $output)
    {
        $stats = $this->getContainer()->get('picture_archive.indexer.statistics')->getStatistics();

        $output->writeln(PHP_EOL . 'files: ' . $stats['files']);

        foreach ($stats['types'] as $type) {
            $output->writeln($type['mimeType'] . ': ' . $type['amount']);
        }
        $output->writeln(PHP_EOL);
    }
}
