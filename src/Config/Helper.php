<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Config;

use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Exception\FileLoaderLoadException;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * Helper is a Facade for the config heavy machinery
 */
class Helper
{

    /**
     * Read the config
     *
     * @param string $dir filepath to the package directory
     *
     * @return array the full config
     *
     * @throws \DomainException if the config is invalid
     */
    public function getConfig($dir)
    {
        // load
        try {
            // all this stuff is not really necessary but this component is kewl
            // and I want to use it.
            // A better configuration handling => better programing
            $delegatingLoader = new DelegatingLoader(new LoaderResolver(array(new Loader())));
            $config = $delegatingLoader->load($dir);
        } catch (FileLoaderLoadException $e) {
            $config = array();
        }
        // validates
        $processor = new Processor();
        $configuration = new Validator();
        try {
            $processedConfig = $processor->processConfiguration($configuration, array($config));
        } catch (InvalidConfigurationException $e) {
            throw new \DomainException($e->getMessage());
        }

        return $processedConfig;
    }

    /**
     * Get the graph configuration
     *
     * @param string $dir filepath to the package directory
     *
     * @return array the full config
     */
    public function getGraphConfig($dir)
    {
        $cfg = $this->getConfig($dir);

        return $cfg['graph'];
    }

}
