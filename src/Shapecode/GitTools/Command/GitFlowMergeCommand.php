<?php

namespace Shapecode\GitTools\Command;

use JBZoo\Utils\Str;
use PHPGit\Exception\GitException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GitFlowMergeCommand
 *
 * @package Shapecode\GitTools\Command
 * @author  Nikita Loges
 */
class GitFlowMergeCommand extends AbstractCommand
{

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('gitflow-merge');

        $this->addOption('all', null);
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
            $this->merge($dir, $message);
        }
    }

    protected function merge(\SplFileInfo $fileInfo)
    {
        $git = $this->repoHelper->getGitRepository($fileInfo);
        $directory = $this->repoHelper->getGitFileInfo($fileInfo);

        dump('Merge Repo: ' . $fileInfo->getFilename());

        $branches = $git->branch();
        $releases = [];

        foreach ($branches as $name => $data) {
            if (Str::isStart($name, 'release/')) {
                $releases[] = $name;
            }
        }

        dump($releases);

        $counter = 0;
        if (count($releases) > 1) {

            foreach ($releases as $release => $data) {
                if ($counter > 0) {
                    $last = $releases[($counter - 1)];
                    $current = $releases[$counter];

                    $git->checkout($current);
                    try {
                        dump('current branch ' . $current . ', merge from ' . $last);
                        $git->merge($last);
                    } catch (GitException $e) {
                        try {
                            $git->merge->abort();
                        } catch (GitException $e) {
                        }
                        dump('abort merging at ' . $current);
                        break;
                    }
                }

                $counter++;
            }
        }
    }

}
