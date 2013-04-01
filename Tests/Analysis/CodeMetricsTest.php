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
            'Impl' => 0, 'Method' => 0, 'Param' => 0,
            'MethodDeclaration' => array('Class' => 0, 'Interface' => 0)
                ), $this->graph->getCardinal());
    }

    public function testMostUsed()
    {
        $center = new Vertex\InterfaceVertex('A');
        $meth = new Vertex\MethodVertex('A::some');
        $this->graph->addEdge($center, $meth);

        for ($k = 0; $k < 20; $k++) {
            $subclass = new Vertex\ClassVertex('B' . $k);
            $impl = new Vertex\ImplVertex($k);
            $this->graph->addEdge($subclass, $center);
            $this->graph->addEdge($subclass, $impl);
            $this->graph->addEdge($impl, $subclass);
        }

        $this->assertEquals(array('Class' => 1, 'Interface' => 1,
            'Impl' => 1, 'Method' => 1, 'Param' => 1,
            'MethodDeclaration' => array('Class' => 0, 'Interface' => 1)
                ), $this->graph->getCardinal());
    }

}