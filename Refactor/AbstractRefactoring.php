<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Refactor;

use Trismegiste\Mondrian\Visitor\FqcnHelper;
use Trismegiste\Mondrian\Parser\PackageParser;
use Trismegiste\Mondrian\Parser\PhpPersistence;

/**
 * Design pattern : Template Method
 * 
 * This is a template for a refactoring service
 */
abstract class AbstractRefactoring
{

    protected $phpDumper;

    /**
     * Build the service with a dumper for writing file
     * 
     * @param \Trismegiste\Mondrian\Parser\PhpPersistence $dumper
     */
    public function __construct(PhpPersistence $dumper)
    {
        $this->phpDumper = $dumper;
    }

    /**
     * Parse and refactor
     *  
     * @param \Iterator $iter list of SplFileInfo
     */
    public function refactor(\Iterator $iter)
    {
        $parser = new PackageParser(new \PHPParser_Parser(new \PHPParser_Lexer()));
        $stmts = $parser->parse($iter);

        // passes :
        $pass = $this->buildRefactoringPass();

        foreach ($pass as $collector) {
            $traverser = new \PHPParser_NodeTraverser();
            $traverser->addVisitor($collector);
            $traverser->traverse($stmts);
        }
    }

    /**
     * Build the refactoring passes
     * 
     * @return FqcnHelper[]
     */
    abstract protected function buildRefactoringPass();
}