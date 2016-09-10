<?php

spl_autoload_register(
    function ($class) {
        if(strpos($class, 'App') !== 0) {
            return;
        }

        /** @noinspection PhpIncludeInspection */
        require_once(__DIR__.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.str_replace(
                '\\',
                DIRECTORY_SEPARATOR,
                $class
            ).'.php');
    }
);