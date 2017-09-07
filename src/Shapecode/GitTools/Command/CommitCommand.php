<?php

namespace Shapecode\GitTools\Command;

use PHPGit\Git;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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

        $this->addOption('depth', null, InputOption::VALUE_OPTIONAL, null, 0);
        $this->addArgument('message', InputArgument::REQUIRED);
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $depth = ($input->getOption('depth') == 'all') ? null : $input->getOption('depth');
        $message = $input->getArgument('message');

        $finder = $this->repoHelper->findRepositories([
            'depth' => $depth
        ]);

        foreach ($finder as $dir) {
            $this->commit($dir, $message);
        }
    }

    /**
     * @param \SplFileInfo $fileInfo
     * @param              $message
     */
    protected function commit(\SplFileInfo $fileInfo, $message)
    {
        /** @var Git $git */
        $git = $this->repoHelper->getGitRepository($fileInfo);

        try {
            $status = $git->status();

            if (count($status['changes'])) {
                foreach ($status['changes'] as $change) {
                    $git->add($change['file']);
                }

                $git->commit($message);
            }
        } catch (\Exception $exception) {
        }
    }

}
