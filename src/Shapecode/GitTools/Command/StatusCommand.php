<?php

namespace Shapecode\GitTools\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class StatusCommand
 *
 * @package Shapecode\GitTools\Command
 * @author  Nikita Loges
 */
class StatusCommand extends AbstractCommand
{

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('status');

        $this->addOption('show-all', 'a');
        $this->addOption('depth', null, InputOption::VALUE_OPTIONAL, null, 2);
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $depth = ($input->getOption('depth') == 'all') ? null : $input->getOption('depth');

        $finder = $this->repoHelper->findRepositories([
            'depth' => $depth
        ]);

        foreach ($finder as $dir) {
            $this->gitStatus($dir);
        }
    }

    /**
     * @param \SplFileInfo $fileInfo
     */
    protected function gitStatus(\SplFileInfo $fileInfo)
    {
        $git = $this->repoHelper->getGitRepository($fileInfo);
        $directory = $this->repoHelper->getGitFileInfo($fileInfo);

        $status = $git->status();

        $count = count($status['changes']);

        $onlyChanges = $this->input->getOption('show-all');

        if ($onlyChanges) {
            $this->io->title($directory->getFilename());

            $branches = $git->branch();
            foreach ($branches as $branch) {
                if ($branch['current']) {
                    break;
                }
            }

            $this->io->text('Path: ' . $directory->getPathname());
            $this->io->text('Current Branch: ' . $branch['name']);
        }

        if ($count > 0) {
            $this->io->note($count . ' Changes found');
            $this->renderChanges($status['changes']);
        } else {
            if ($onlyChanges) {
                $this->io->success('No Changes detected');
            }
        }
    }

    /**
     * @param $changes
     */
    protected function renderChanges($changes)
    {
        $headers = ['File', 'Index', 'Work Tree'];
        $rows = [];

        foreach ($changes as $change) {
            $rows[] = [
                $change['file'],
                $change['index'],
                $change['work_tree'],
            ];
        }

        $this->io->table($headers, $rows);
    }

}
