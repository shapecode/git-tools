<?php

namespace Tenolo\GitTools\Command;

use PHPGit\Git;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tenolo\GitTools\Helper\RepositoryHelper;

/**
 * Class PushCommand
 *
 * @package Tenolo\GitTools\Command
 * @author  Nikita Loges
 * @company tenolo GbR
 */
class PushCommand extends Command
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
        $this->setName('push');

        $this->addOption('all', null);
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
        $depth = ($input->getOption('all')) ? null : 0;

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
        /** @var Git $git */
        $git = $this->repoHelper->getGitRepository($fileInfo);
        $directory = new \SplFileInfo(dirname($fileInfo->getPathname()));

        try {
            $this->io->title($directory->getFilename());

            $git->pull();
            $git->push();

            $this->io->success('branch successfully pushed');
        } catch (\Exception $exception) {
            $this->io->error($exception->getMessage());
        }
    }

}
