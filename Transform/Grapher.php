<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform;

use Trismegiste\Mondrian\Visitor;

/**
 * Grapher transforms source code into graph
 */
class Grapher
{

    public function __construct(/* Finder */)
    {
        
    }

    public function parse($iter)
    {
        $parser = new \PHPParser_Parser(new \PHPParser_Lexer());
        $graph = new \Trismegiste\Mondrian\Graph\Digraph();

        $context = new Context($graph);
        // 0th pass
        $pass[0] = new Visitor\SymbolMap($context);
        // 1st pass
        $pass[1] = new Visitor\VertexCollector($context);
        // 2nd pass
        $pass[2] = new Visitor\EdgeCollector($context);

        foreach ($pass as $collector) {
            $stopWatch = time();

            $traverser = new \PHPParser_NodeTraverser();
            $traverser->addVisitor($collector);

            foreach ($iter as $fch) {
                $code = file_get_contents($fch);
                $stmts = $parser->parse($code);
                $traverser->traverse($stmts);
            }

            $collector->compile();
            //   printf("Pass in %d sec\n", time() - $stopWatch);
        }

        return $graph;
    }

}