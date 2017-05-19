<?php

namespace Shapecode\GitTools\Command;

use PHPGit\Git;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CommitCommand
 *
 * @package Shapecode\GitTools\Command
 * @author  Nikita Loges
 */
class CommitCommand extends AbstractCommand
{

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('commit');

        $this->addOption('all', null);
        $this->addArgument('message', InputArgument::REQUIRED);
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $depth = ($input->getOption('all')) ? null : 0;
        $message = $input->getArgument('message');

        $finder = $this->repoHelper->findRepositories([
            'depth' => $depth
        ]);

        foreach ($finder as $dir) {
            $this->gitStatus($dir, $message);
        }
    }

    /**
     * @param \SplFileInfo $fileInfo
     * @param              $message
     */
    protected function gitStatus(\SplFileInfo $fileInfo, $message)
    {
        /** @var Git $git */
        $git = $this->repoHelper->getGitRepository($fileInfo);
        $directory = new \SplFileInfo(dirname($fileInfo->getPathname()));

        try {
            $status = $git->status();

            if (count($status['changes'])) {
                foreach ($status['changes'] as $change) {
                    $git->add($change['file']);
                }

                $git->commit($message);
            }
        } catch (\Exception $exception) {
            dump($exception);
        }
    }

}
