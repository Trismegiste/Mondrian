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

        $reflection = new ReflectionContext();
        $vertexContext = new GraphContext();
        // 0th pass
        $pass[0] = new Visitor\SymbolMap($reflection);
        // 1st pass
        $pass[1] = new Visitor\VertexCollector($reflection, $vertexContext, $graph);
        // 2nd pass
        $pass[2] = new Visitor\EdgeCollector($reflection, $vertexContext, $graph);

        $stmts = $parser->parse($iter);

        foreach ($pass as $collector) {

            $traverser = new \PHPParser_NodeTraverser();
            $traverser->addVisitor($collector);
            $traverser->traverse($stmts);
        }

        return $graph;
    }

}