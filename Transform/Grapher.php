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

        // for memory concerns, I'll re-parse files on each pass
        // (slower but lighter) and enriching the Context
        foreach ($pass as $collector) {
            $stopWatch = time();

            $traverser = new \PHPParser_NodeTraverser();
            $traverser->addVisitor($collector);

            $stmts = $parser->parse($iter);
            $traverser->traverse($stmts);

            $collector->compile();
            //   printf("Pass in %d sec\n", time() - $stopWatch);
        }

        return $graph;
    }

}