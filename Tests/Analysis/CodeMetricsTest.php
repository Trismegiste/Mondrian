<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Analysis;

use Trismegiste\Mondrian\Analysis\CodeMetrics;
use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Transform\Vertex;

/**
 * CodeMetricsTest tests CodeMetrics analysis
 *
 */
class CodeMetricsTest extends \PHPUnit_Framework_TestCase
{

    protected $graph;

    protected function setUp()
    {
        $this->graph = new CodeMetrics(new Digraph());
    }

    public function testEmptyGraph()
    {
        $this->assertEquals(array('Class' => 0, 'Interface' => 0,
            'Impl' => 0, 'Method' => 0, 'Param' => 0, 'Trait' => 0,
            'MethodDeclaration' => array('Class' => 0, 'Interface' => 0, 'Trait' => 0)
                ), $this->graph->getCardinal());
    }

    public function testMostUsed()
    {
        // interface A
        $center = new Vertex\InterfaceVertex('A');
        $meth = new Vertex\MethodVertex('A::some');
        $param = new Vertex\ParamVertex('A::some/0');
        // A has a method some
        $this->graph->addEdge($center, $meth);
        $this->graph->addEdge($meth, $param);
        // class B
        $subclass = new Vertex\ClassVertex('B');
        // B implements A
        $this->graph->addEdge($subclass, $center);
        $impl = new Vertex\ImplVertex('B::some');
        // implementation of A::some depends on :
        $this->graph->addEdge($subclass, $impl);
        $this->graph->addEdge($impl, $subclass);
        $this->graph->addEdge($impl, $param);

        $this->assertEquals(array('Class' => 1, 'Interface' => 1,
            'Impl' => 1, 'Method' => 1, 'Param' => 1, 'Trait' => 0,
            'MethodDeclaration' => array('Class' => 0, 'Interface' => 1, 'Trait' => 0)
                ), $this->graph->getCardinal());
    }

}
