<?php

namespace Shapecode\GitTools\Command;

use Shapecode\GitTools\Helper\RepositoryHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class AbstractCommand
 *
 * @package Shapecode\GitTools\Command
 * @author  Nikita Loges
 */
class AbstractCommand extends Command
{

    /** @var InputInterface */
    protected $input;

    /** @var OutputInterface */
    protected $output;

    /** @var SymfonyStyle */
    protected $io;

    /** @var RepositoryHelper */
    protected $repoHelper;

    protected function configure()
    {
        $this->addOption('depth', null, InputOption::VALUE_OPTIONAL, null, 0);
        $this->addOption('all', 'a', InputOption::VALUE_NONE);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->io = new SymfonyStyle($input, $output);
        $this->repoHelper = new RepositoryHelper();
    }

    /**
     * @param InputInterface $input
     *
     * @return mixed|null
     */
    protected function getDepth(InputInterface $input)
    {
        $depth = ($input->getOption('depth') == 'all') ? null : $input->getOption('depth');

        if ($input->getOption('all')) {
            $depth = null;
        }

        return $depth;
    }
}
