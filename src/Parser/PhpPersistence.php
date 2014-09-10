<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Parser;

/**
 * PhpPersistence is an abstract template for persisting a PhpFile
 */
abstract class PhpPersistence
{

    protected $prettyPrinter;

    public function __construct(/* logger, output ? */)
    {
        $this->prettyPrinter = new \PHPParser_PrettyPrinter_Default();
    }

    /**
     * Persist the file
     *
     * @param \Trismegiste\Mondrian\Parser\PhpFile $aFile
     */
    abstract public function write(PhpFile $aFile);
}
