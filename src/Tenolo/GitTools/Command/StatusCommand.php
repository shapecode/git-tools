<?php

namespace Tenolo\GitTools\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tenolo\GitTools\Helper\RepositoryHelper;

/**
 * Class StatusCommand
 *
 * @package Tenolo\GitTools\Command
 * @author  Nikita Loges
 * @company tenolo GbR
 */
class StatusCommand extends Command
{

    /** @var InputInterface */
    protected $input;

    /** @var OutputInterface */
    protected $output;

    /** @var SymfonyStyle */
    protected $io;

    /** @var RepositoryHelper */
    protected $repoHelper;

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('status');

        $this->addOption('only-changes', 'oc');
        $this->addOption('depth', null, InputOption::VALUE_OPTIONAL, null, 1);
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->io = new SymfonyStyle($input, $output);
        $this->repoHelper = new RepositoryHelper();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $depth = (int)$input->getOption('depth');
        $depth = ($depth == 0) ? 0 : '<= ' . $depth;

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
        $directory = new \SplFileInfo(dirname($fileInfo->getPathname()));

        $status = $git->status();

        $count = count($status['changes']);

        $onlyChanges = (!$this->input->getOption('only-changes') || $count > 0);

        if ($onlyChanges) {
            $this->io->title($directory->getFilename());

            $branches = $git->branch();
            foreach ($branches as $branch) {
                if ($branch['current']) {
                    break;
                }
            }

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
