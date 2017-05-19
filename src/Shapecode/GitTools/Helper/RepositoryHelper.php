<?php

namespace Shapecode\GitTools\Helper;

use PHPGit\Git;
use Symfony\Component\Finder\Finder;

/**
 * Class RepositoryHelper
 *
 * @package Shapecode\GitTools\Helper
 * @author  Nikita Loges
 */
class RepositoryHelper
{

    /**
     * @param array $options
     *
     * @return Finder
     */
    public function findRepositories(array $options = [])
    {
        if (!isset($options['working_directory']) || is_null($options['working_directory'])) {
            $workingDirectory = getcwd();
        } else {
            $workingDirectory = $options['working_directory'];
        }

        $finder = new Finder();
        $finder->directories();
        $finder->ignoreDotFiles(false);
        $finder->ignoreVCS(false);
        $finder->name('.git');
        $finder->in($workingDirectory);

        if (isset($options['depth']) && !is_null($options['depth'])) {
            $finder->depth($options['depth']);
        }

        $finder->sortByName();

        return $finder;
    }

    /**
     * @param \SplFileInfo $fileInfo
     *
     * @return Git
     */
    public function getGitRepository(\SplFileInfo $fileInfo)
    {
        $git = new Git();
        $git->setRepository($fileInfo->getPathname() . '/..');

        $git->config->set('user.email', 'git@nikita-loges');
        $git->config->set('user.name', 'Nikita Loges');

        return $git;
    }
}
