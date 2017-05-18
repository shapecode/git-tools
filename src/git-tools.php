#!/usr/bin/env php
<?php
require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\Application;
use Tenolo\GitTools\Command as GitCommand;

$application = new Application('git-tools', '1.0.0');

$application->add(new GitCommand\StatusCommand());
$application->add(new GitCommand\CommitCommand());
$application->add(new GitCommand\PushCommand());

$application->run();
