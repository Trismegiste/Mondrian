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

    public function load($resource, $type = null)
    {
        return Yaml::parse($resource . DIRECTORY_SEPARATOR . $this->default);
    }

    public function supports($resource, $type = null)
    {
        return file_exists($resource . DIRECTORY_SEPARATOR . $this->default);
    }

}