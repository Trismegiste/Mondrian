<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Transform\Format;

use Trismegiste\Mondrian\Transform\Format\Graphviz;
use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Transform\Vertex\ClassVertex;

/**
 * GraphvizTest is a test for Graphviz decorator
 */
class GraphvizTest extends \PHPUnit_Framework_TestCase
{

    protected $graph;

    protected function setUp()
    {
        $graph = new Digraph();
        for ($k = 0; $k < 5; $k++) {
            $set[] = new ClassVertex('Guess\What\I\Draw' . $k);
        }
        for ($k = 0; $k < 5; $k++) {
            $graph->addEdge($set[(2 * $k) % 5], $set[(2 * $k + 2) % 5]);
        }
        $this->graph = new Graphviz($graph);
    }

    public function testGenerate()
    {
        $content = $this->graph->export();
        $this->assertStringStartsWith('digraph', $content);
        echo $content;
    }

}