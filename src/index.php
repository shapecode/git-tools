<?php

include_once('../vendor/autoload.php');

use PHPGit\Git;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Tenolo\Utilities\Utils\StringUtil;

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

function merge(\SplFileInfo $fileInfo)
{
    $fs = new Filesystem();
    if (!$fs->exists($fileInfo->getPathname() . '/.git')) {
        return;
    }

    dump('Merge Repo: ' . $fileInfo->getFilename());

    $git = new Git();
    $git->setRepository($fileInfo->getPathname());

    $git->config->set('user.email', 'git@nikita-loges');
    $git->config->set('user.name', 'Nikita Loges');

    $branches = $git->branch();
    $releases = [];

    foreach ($branches as $name => $data) {
        if (StringUtil::startsWith($name, 'release/')) {
            $releases[] = $name;
        }
    }

    dump($releases);

    $counter = 0;
    if (count($releases) > 1) {

        foreach ($releases as $release => $data) {
            if ($counter > 0) {
                $last = $releases[($counter - 1)];
                $current = $releases[$counter];

                $git->checkout($current);
                try {
                    dump('current branch ' . $current . ', merge from ' . $last);
                    $git->merge($last);
                } catch (PHPGit\Exception\GitException $e) {
                    try {
                        $git->merge->abort();
                    } catch (PHPGit\Exception\GitException $e) {
                    }
                    dump('abort merging at ' . $current);
                    break;
                }
            }

            $counter++;
        }
    }
}
