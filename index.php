<?php

/**
 * THIS IS A FRONTEND CONTROLLER CLASS FOR OUR EXAMPLES.
 */
require_once(__DIR__.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php');
require_once(__DIR__.DIRECTORY_SEPARATOR.'__autoload.php');

use \PhpImap\Dependency\Container\Loader\Basic\BasicDependencyContainerLoader;
use \PhpImap\Dependency\Container\Loader\Cached\CachedDependencyContainerLoader;

/**
 * This is the dependency container initiation. It uses Symfony DependencyInjection Component inside.
 * @see http://symfony.com/doc/current/components/dependency_injection.html
 * I highly recommend to use any dependy injection framework as it will be really hard to manage all the dependencies
 * without it.
 */
$basicLoader = new BasicDependencyContainerLoader();

/**
 * change the cache file path to your cache directory
 */
$cacheFile = new SplFileInfo(__DIR__.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'container.php');

/**
 * change this with some environmental value that you have on your web server
 */
$isDebugMode = new SplBool(false);

/**
 * For convenience I've added 2 config files that are not part of the package.
 *
 * parameters.yml - to contain my login parameters and to overload the default parameters. Default parameters are here:
 * vendor/drakonli/php-imap/src/PhpImap/Dependency/Resources/configs/parameters.yml
 */
$parameters = __DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'App'.DIRECTORY_SEPARATOR.'resources'
    .DIRECTORY_SEPARATOR.'configs'.DIRECTORY_SEPARATOR.'parameters.yml';

/**
 * services.yml - to add my own dependencies and to inject services from the vendor.
 */
$services = __DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'App'.DIRECTORY_SEPARATOR.'resources'
    .DIRECTORY_SEPARATOR.'configs'.DIRECTORY_SEPARATOR.'services.yml';

/**
 * We actually instantiate cached container, so that our dependencies don't build on every request
 */
$container = (new CachedDependencyContainerLoader($basicLoader, $cacheFile, $isDebugMode))
    ->loadContainer([new \SplFileInfo($parameters), new \SplFileInfo($services)]);

/**
 * This next part just gets our example classes from container and executes them
 */

/**
 * @var \App\EmailDisplayer $mailDisplayer
 */
$mailDisplayer = $container->get('app.email_displayer');

$login = $container->getParameter('gmailLogin');
$password = $container->getParameter('gmailPassword');

$mailDisplayer->showLetters($login, $password);
