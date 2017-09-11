<?php

namespace Shapecode\GitTools\Command;

use PHPGit\Git;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class LogCommand
 *
 * @package Shapecode\GitTools\Command
 * @author  Nikita Loges
 */
class LogCommand extends AbstractCommand
{

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('log');

        $this->addOption('depth', null, InputOption::VALUE_OPTIONAL, null, 2);
        $this->addOption('date', null, InputOption::VALUE_REQUIRED, null, 'now');
        $this->addOption('period', null, InputOption::VALUE_OPTIONAL, null, '1 day');
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
            $this->repository($input, $output, $dir);
        }
    }

    /**
     * @param \SplFileInfo $fileInfo
     */
    protected function repository(InputInterface $input, OutputInterface $output, \SplFileInfo $fileInfo)
    {
        $date = new \DateTime($input->getOption('date'));
        $date->setTime(0, 0, 0);

        $until = clone $date;
        $until->modify('+' . $input->getOption('period'));

        /** @var Git $git */
        $git = $this->repoHelper->getGitRepository($fileInfo);
        $directory = $this->repoHelper->getGitFileInfo($fileInfo);

        try {

            $builder = $git->getProcessBuilder();
            $builder->add('log');
            $builder->add('--format=%H || %aN || %aE || %aD || %s');

            $builder->add('--after="' . $date->format('Y-m-d H:i') . '"');
            $builder->add('--before="' . $until->format('Y-m-d H:i') . '"');

            $gitOutput = $git->run($builder->getProcess());

            if (!empty($gitOutput)) {
                $this->io->title($directory->getFilename());
                $this->io->write($gitOutput);
            }

        } catch (\Exception $exception) {
            $this->io->error($exception->getMessage());
        }
    }

}
