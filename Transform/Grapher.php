<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform;

use Trismegiste\Mondrian\Visitor;
use Trismegiste\Mondrian\Parser\PackageParser;
use Trismegiste\Mondrian\Graph\Digraph;

/**
 * Grapher transforms source code into graph
 */
class Grapher
{

    protected $config;

    public function __construct(array $cfg)
    {
        $this->config = $cfg;
    }

    /**
     * Parse a set of php files and build a digraph with multiple
     * passes by visiting the nodes in source code
     *
     * @param \Iterator $iter list of SplFileInfo
     *
     * @return \Trismegiste\Mondrian\Graph\Digraph
     */
    public function build(\Iterator $iter)
    {
        $graph = new Digraph();

        $parser = new PackageParser(new \PHPParser_Parser(new \PHPParser_Lexer()));
        $stmts = $parser->parse($iter);

        // passes :
        $pass = $this->buildCompilerPass($graph);

        foreach ($pass as $collector) {
            $traverser = new \PHPParser_NodeTraverser();
            $traverser->addVisitor($collector);
            $traverser->traverse($stmts);
        }

        return $graph;
    }

    protected function buildCompilerPass(Digraph $graph)
    {
        $reflection = new ReflectionContext();
        $vertexContext = new GraphContext($this->config);

        return array(
            new Visitor\SymbolMap($reflection),
            new Visitor\VertexCollector($reflection, $vertexContext, $graph),
            new Visitor\EdgeCollector($reflection, $vertexContext, $graph)
        );
    }

}
