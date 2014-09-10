<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Parser;

/**
 * BuilderFactory is an abstract factory
 */
class BuilderFactory extends \PHPParser_BuilderFactory
{

    public function file($name)
    {
        return new PhpFileBuilder($name);
    }

}