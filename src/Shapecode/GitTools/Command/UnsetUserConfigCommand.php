<?php

namespace Shapecode\GitTools\Command;

use PHPGit\Git;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Class PushCommand
 *
 * @package Shapecode\GitTools\Command
 * @author  Nikita Loges
 */
class UnsetUserConfigCommand extends AbstractCommand
{

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('unset-user-config');

        $this->addOption('all', 'a');
        $this->addOption('depth', null, InputOption::VALUE_OPTIONAL, null, 0);
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $depth = ($input->getOption('all')) ? null : $input->getOption('depth');

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
        $directory = $this->repoHelper->getGitFileInfo($fileInfo);

        try {
            $this->io->title($directory->getFilename());

            $builder = ProcessBuilder::create()
                ->setPrefix('git')
                ->setWorkingDirectory($directory->getPathname());

            $builder
                ->add('config')
                ->add('--remove-section')
                ->add('user');

            $builder->getProcess()->run();

            $this->io->success('config removed');
        } catch (\Exception $exception) {
            $this->io->error($exception->getMessage());
        }
    }

}
