<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Plugin;

use Symfony\Component\Console\Application as SymfoApp;

/**
 * Application is an symfony app with plugin capabilities
 */
class Application extends SymfoApp
{

    /**
     * Adds a list of subclasses of Command
     *
     * @param array $listing an array of fqcn symfony command
     */
    public function addPlugin(array $listing)
    {
        foreach ($listing as $cmd) {
            if (is_subclass_of($cmd, 'Symfony\Component\Console\Command\Command')) {
                $this->add(new $cmd());
            } else {
                throw new \InvalidArgumentException("$cmd is not a Symfony Command");
            }
        }
    }

}