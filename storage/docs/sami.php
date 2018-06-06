<?php

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('tests')
    ->in(__DIR__.'/../../src');

return new Sami($iterator, array(
    'title'                => 'laravel-remedy',
    'build_dir'            => __DIR__.'/build',
    'cache_dir'            => __DIR__.'/cache',
    #'remote_repository'    => new GitHubRemoteRepository('username/repository', '/path/to/repository'),
    'default_opened_level' => 2,
));