<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Plugin;

use Symfony\Component\Console\Application as SymfoApp;

/**
 * Application is a ...
 *
 * @author florent
 */
class Application extends SymfoApp
{

    /**
     * Adds a list of subclasses of Command
     *
     * @param array $listing
     */
    public function addPlugin(array $listing)
    {
        foreach ($listing as $cmd) {
            if (is_subclass_of($cmd, 'Symfony\Component\Console\Command\Command', true)) {
                $this->add(new $cmd());
            } else {
                throw new \InvalidArgumentException("$cmd is not a Symfony Command");
            }
        }
    }

}