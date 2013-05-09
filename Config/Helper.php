<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Config;

use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Exception\FileLoaderLoadException;

/**
 * Helper is a Facade for the config heavy machinery
 */
class Helper
{

    public function getConfig($dir)
    {
        // load
        try {
            $delegatingLoader = new DelegatingLoader(new LoaderResolver(array(new Loader())));
            $config = $delegatingLoader->load($dir);
        } catch (FileLoaderLoadException $e) {
            $config = array();
        }
        // validates
        $processor = new Processor();
        $configuration = new Validator();

        return $processor->processConfiguration($configuration, array($config));
    }

}