<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Builder;

use Trismegiste\Mondrian\Builder\Compiler;
use Trismegiste\Mondrian\Builder\Statement;

/**
 * Linking is making the link between the parser and the compiler
 *
 * It is a kind of director, but with directors and eventually it is a Facade
 */
class Linking
{

    protected $parser;
    protected $compiler;

    public function __construct(Statement\BuilderInterface $parserBuilder, Compiler\BuilderInterface $compilerBuilder)
    {
        $this->parser = new Statement\Director($parserBuilder);
        $this->compiler = new Compiler\Director($compilerBuilder);
    }

    public function run(\Iterator $iter)
    {
        $stmts = $this->parser->parse($iter);
        $this->compiler->compile($stmts);
    }

}