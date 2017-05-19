<?php

include_once('../vendor/autoload.php');

use PHPGit\Git;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use JBZoo\Utils\Str;

Debug::enable();

$fs = new Filesystem();
$finder = new Finder();
$finder
    ->directories()
    ->in(__DIR__ . '/../../../tenolo')
    ->depth(0)
    ->sortByName();

$dirCoutner = 0;
foreach ($finder as $dir) {
    $dirCoutner++;

    if ($dirCoutner > 5) {
        break;
    }

    merge($dir);
}
die();
