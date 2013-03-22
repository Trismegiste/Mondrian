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
        $vertex = array('class' => array(), 'interface' => array(),
            'method' => array(), 'impl' => array(),
            'param' => array()
        );
        // 1st pass
        $pass[0] = new Visitor\VertexCollector($graph, $vertex);
        // 2nd pass
        $pass[1] = new Visitor\EdgeCollector($graph, $vertex);

        foreach ($pass as $collector) {
            $traverser = new \PHPParser_NodeTraverser();
            $traverser->addVisitor($collector);

            foreach ($iter as $fch) {
                $code = file_get_contents($fch);
                $stmts = $parser->parse($code);
                $traverser->traverse($stmts);
            }
        }

        return $graph;
    }

}