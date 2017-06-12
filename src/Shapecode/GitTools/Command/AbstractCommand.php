<?php

namespace Shapecode\GitTools\Command;

use Shapecode\GitTools\Helper\RepositoryHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
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
}
