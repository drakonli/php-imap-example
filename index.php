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
 * I highly recommend to use any dependency injection framework as it will be really hard to manage all the dependencies
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
 * Crete different and execute different examples by different requests
 */
switch ($_SERVER['REQUEST_URI']) {
    case '/example_with_non_strict_factory_method':

        /**
         * @var \App\NonStrictFactoryMethodEmailDisplayer $mailDisplayer
         */
        $mailDisplayer = $container->get('app.email_displayer.non_strict_factory_method');
        $mailDisplayer->showLetters();

        break;

    case '/example_with_builders':
        /**
         * @var \App\BuildersEmailDisplayer $mailDisplayer
         */
        $mailDisplayer = $container->get('app.email_displayer_with_builders');
        $mailDisplayer->showLetters();

        break;

    case '/example_with_predefined_connection_factory':
        /**
         * @var \App\PreDefinedConnectionFactoryEmailDisplayer $mailDisplayer
         */
        $mailDisplayer = $container->get('app.email_displayer.pre_defined_connection');
        $mailDisplayer->showLetters();

        break;

    case '/example_with_injected_connection':
        /**
         * @var \App\ConnectionInjectedEmailDisplayer $mailDisplayer
         */
        $mailDisplayer = $container->get('app.email_displayer.connection_injection');
        $mailDisplayer->showLetters();

        break;

    default:
        throw new \Exception('No request matched');

        break;
}
