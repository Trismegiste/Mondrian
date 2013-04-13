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

    public function testEmpty()
    {
        $exporter = new Graphviz(new Digraph());
        $content = $exporter->export();
        $this->assertStringStartsWith('digraph', $content);
    }

    public function testGenerate()
    {
        $graph = new NotPlanar();

        $dumper = $this->getMockBuilder('Alom\Graphviz\Digraph')
                ->setMethods(array('edge', 'node', 'subgraph'))
                ->setConstructorArgs(array('Mockuped'))
                ->getMock();
        $dumper->expects($this->exactly(5))
                ->method('edge');
        $dumper->expects($this->exactly(5))
                ->method('node');
        $dumper->expects($this->once())
                ->method('subgraph')
                ->will($this->returnValue(new \Alom\Graphviz\Subgraph('a')));

        $exporter = new Graphviz($graph);
        $exporter = $this->getMockBuilder('Trismegiste\Mondrian\Transform\Format\Graphviz')
                ->setMethods(array('createGraphVizDot'))
                ->setConstructorArgs(array($graph))
                ->getMock();
        $exporter->expects($this->once())
                ->method('createGraphVizDot')
                ->will($this->returnValue($dumper));

        $content = $exporter->export();
        $this->assertStringStartsWith('digraph', $content);
    }

}

class NotPlanar extends Digraph
{

    public function __construct()
    {
        parent::__construct();
        for ($k = 0; $k < 5; $k++) {
            $set[] = new ClassVertex('Guess\What\I\Draw' . $k);
        }
        for ($k = 0; $k < 5; $k++) {
            $this->addEdge($set[(2 * $k) % 5], $set[(2 * $k + 2) % 5]);
        }
    }

    public function getPartition()
    {
        return array($this->getVertexSet());
    }

}