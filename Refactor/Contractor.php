<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Refactor;

use Trismegiste\Mondrian\Visitor;
use Symfony\Component\Finder\SplFileInfo;
use Trismegiste\Mondrian\Parser\PackageParser;
use Trismegiste\Mondrian\Parser\PhpDumper;

/**
 * Contractor refactors a list of classes with annotations hints.
 * 
 * What it does ?
 *  * It creates a new interface for each class with annotation 
 *    like "@mondrian contractor NewInterfaceName".
 *  * it replaces all these classes by their new interface in 
 *    methods parameters (public or not, this is important)
 *  * it adds the inheritance for NewInterfaceName
 * 
 * Each interface is stored in the same namespace, neighbour of the
 * class in a directory. NewInterfaceName is a short name not a FQCN.
 * It is not possible to store the generated content in another directory 
 * since everybody uses Git or at least SVN. Therefore you can launch the
 * test suite immediatly.
 * 
 */
class Contractor
{

    protected $phpDumper;

    /**
     * Build the service with a dumper for writeing file
     * 
     * @param \Trismegiste\Mondrian\Parser\PhpDumper $dumper
     */
    public function __construct(PhpDumper $dumper)
    {
        $this->phpDumper = $dumper;
    }

    /**
     * Parse and refactor
     *  
     * @param \Iterator $iter list of absolute path to files to parse
     */
    public function refactor(\Iterator $iter)
    {
        $parser = new PackageParser(new \PHPParser_Parser(new \PHPParser_Lexer()));
        $context = new Refactored();
        // passes :
        // finds which class must be refactored (and add inheritance)
        $pass[0] = new Visitor\NewContractCollector($context);
        // replaces the parameters types with the interface
        $pass[1] = new Visitor\ParamRefactor($context);
        // creates the new interface file
        $pass[2] = new Visitor\InterfaceExtractor($context, $this->phpDumper);

        $stmts = $parser->parse($iter);

        foreach ($pass as $collector) {
            $traverser = new \PHPParser_NodeTraverser();
            $traverser->addVisitor($collector);
            $traverser->traverse($stmts);
        }
    }

}