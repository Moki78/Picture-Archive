<?php

namespace PictureArchiveBundle\Command;

use Doctrine\Common\Collections\ArrayCollection;
use PictureArchiveBundle\Entity\MediaFile;
use PictureArchiveBundle\Index\Processor;
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
    protected function configure()
    {
        $this
            ->setName('picturearchive:indexer')
            ->setDescription('Picture Archive Indexer')
            ->addOption('reindex', null, InputOption::VALUE_NONE, 'reindex all')
            ->addOption('conflicts', null, InputOption::VALUE_NONE, 'list files with the same hashes')
            ->addOption('fix', null, InputOption::VALUE_NONE, 'fix confliced files')
            ->addOption('progress', 'p', InputOption::VALUE_NONE, 'show progressbar');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
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

//        if ($input->getOption('conflicts')) {
//            $conflictedHashes = $processor->getConflictedHashes();
//
//            $output->writeln('found ' . $conflictedHashes->count() . ' conflicted hashes');
//
//            foreach ($conflictedHashes as $hash => $items) {
//                echo PHP_EOL . $hash . ':' . PHP_EOL;
//
//                if ($input->getOption('fix')) {
//                    $this->askForDeletion($input, $output, $hash, $items);
//                } else {
//                    $items->forAll(
//                        function ($key, MediaFile $file) {
//                            printf(
//                                "    %s - %s" . PHP_EOL,
//                                $file->getMediaDate()->format('Y-m-d H:i:s'),
//                                $file->getPath()
//                            );
//                            return true;
//                        }
//                    );
//                }
//            }
//
//            return 0;
//        }

        $this->showStats($output);
        return 0;
    }

    /**
     * @param OutputInterface $output
     */
    private function showStats(OutputInterface $output)
    {
        $stats = $this->getContainer()->get('picture_archive.index.statistics')->getStatistics();


        $output->writeln("\nfiles: " . $stats['files']);

        foreach ($stats['types'] as $type) {
            $output->writeln($type['mimeType'] . ": " . $type['amount']);
        }
        $output->writeln("\n");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $hash
     * @param ArrayCollection $items
     * @return bool
     */
    private function askForDeletion(
        InputInterface $input,
        OutputInterface $output,
        $hash,
        ArrayCollection $items
    )
    {
        $choices = array();
        $index = 0;
        foreach ($items as $file) {
            $choices[++$index] = $file->getFilepath();
        }
        $choices['i'] = "ignore, go to next";


        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'which files should be delete?',
            $choices,
            'i'
        );
        $question->setMultiselect(true);

        $selected = $helper->ask($input, $output, $question);
        if (in_array('i', $selected)) {
            $output->writeln('hash ignored, do nothing');
            return;
        }

        if (count($selected) == $items->count()) {
            $output->writeln('could not delete all files');
            return;
        }

        /** @var Processor $processor */
        $processor = $this->getContainer()->get('picture_archive.index.processor');

        foreach ($selected as $index) {
            $filepath = $choices[$index];
            foreach ($items as $file) {
                if ($filepath == $file->getFilepath()) {
                    $output->writeln("remove file " . $file->getFilepath());

                    if ($processor->deleteConflictedFile($file)) {
                        $items->removeElement($file);

                        $output->writeln("file removed");
                    } else {
                        $output->writeln("could not remove file");
                    }
                }
            }
        }
        if ($items->count() == 1) {
            $items->first()->setStatus(MediaFile::STATUS_IMPORTED);
            $output->writeln("the hash is not conflicted anymore");
        }

        $this->getContainer()->get('doctrine.orm.entity_manager')->flush();
        return;
    }
}
