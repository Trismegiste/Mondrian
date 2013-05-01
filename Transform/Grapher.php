<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform;

use Trismegiste\Mondrian\Visitor;
use Trismegiste\Mondrian\Parser\PackageParser;

/**
 * Grapher transforms source code into graph
 */
class Grapher
{

    /**
     * Parse a set of php files and build a digraph with multiple
     * passes by visiting the nodes in source code
     *  
     * @param \Iterator $iter list of SplFileInfo
     * 
     * @return \Trismegiste\Mondrian\Graph\Digraph 
     */
    public function parse(\Iterator $iter)
    {
        $parser = new PackageParser(new \PHPParser_Parser(new \PHPParser_Lexer()));
        $graph = new \Trismegiste\Mondrian\Graph\Digraph();

        $context = new Context();
        // 0th pass
        $pass[0] = new Visitor\SymbolMap($context);
        // 1st pass
        $pass[1] = new Visitor\VertexCollector($context, $graph);
        // 2nd pass
        $pass[2] = new Visitor\EdgeCollector($context, $graph);

        $stmts = $parser->parse($iter);

        foreach ($pass as $collector) {

            $traverser = new \PHPParser_NodeTraverser();
            $traverser->addVisitor($collector);
            $traverser->traverse($stmts);

            $collector->compile();
        }

        return $graph;
    }

}