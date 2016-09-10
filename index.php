<?php

require_once(__DIR__.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php');
require_once(__DIR__.DIRECTORY_SEPARATOR.'__autoload.php');

use \PhpImap\Dependency\Container\Loader\Basic\BasicDependencyContainerLoader;
use \PhpImap\Dependency\Container\Loader\Cached\CachedDependencyContainerLoader;

$basicLoader = new BasicDependencyContainerLoader();
$cacheFile = new SplFileInfo(__DIR__.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'container.php');
$isDebugMode = new SplBool(true);
$container = (new CachedDependencyContainerLoader($basicLoader, $cacheFile, $isDebugMode))
    ->loadContainer(
        [
            new \SplFileInfo(
                __DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'parameters.yml'
            ),
            new \SplFileInfo(
                __DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'services.yml'
            ),
        ]
    );

/**
 * @var \App\EmailDisplayer $mailDisplayer
 */
$mailDisplayer = $container->get('app.email_displayer');
$mailDisplayer->showLetters($container->getParameter('gmailLogin'), $container->getParameter('gmailPassword'));
