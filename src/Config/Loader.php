<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Config;

use Symfony\Component\Config\Loader\Loader as SymfonyLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * Loader is a loader for the Mondrian config tool
 */
class Loader extends SymfonyLoader
{

    protected $default = '.mondrian.yml';

    /**
     *
     * @param string $resource the directory which owns the .mondrian.yml file
     *
     * @return array the config
     */
    public function load($resource, $type = null)
    {
        return Yaml::parse($resource . DIRECTORY_SEPARATOR . $this->default);
    }

    /**
     * Does this loader support
     *
     * @param string $resource directory path
     *
     * @return bool true if the config file is here
     */
    public function supports($resource, $type = null)
    {
        return file_exists($resource . DIRECTORY_SEPARATOR . $this->default);
    }

}
