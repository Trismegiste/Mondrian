<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Graph;

use Trismegiste\Mondrian\Graph\Vertex;

/**
 * GraphTest is a template test for a graph subclass
 */
abstract class GraphTest extends \PHPUnit_Framework_TestCase
{

    protected $graph;

    protected function setUp()
    {
        $this->graph = $this->createGraphInstance();
    }

    abstract protected function createGraphInstance();

    protected function tearDown()
    {
        unset($this->graph);
    }

    public function testAddEdge()
    {
        $this->graph->addEdge(new Vertex('A'), new Vertex('B'));
        $this->checkSimpleGraph(2, 1);
    }

    public function testAddEdgeOnExisting()
    {
        $v1 = new Vertex('A');
        $v2 = new Vertex('B');
        $this->graph->addVertex($v1);
        $this->graph->addVertex($v2);
        $this->graph->addEdge($v1, $v2);
        $this->checkSimpleGraph(2, 1);
        // adding an edge between two vertices already connected...
        $this->graph->addEdge($v1, $v2);
        // ... does not change anything
        $this->checkSimpleGraph(2, 1);
    }

    public function testSearchEdge()
    {
        $v1 = new Vertex('A');
        $v2 = new Vertex('B');
        $this->graph->addEdge($v1, $v2);
        $this->assertNotNull($this->graph->searchEdge($v1, $v2));
        $this->assertNull($this->graph->searchEdge($v1, new Vertex('C')));
    }

    protected function checkSimpleGraph($vCard, $eCard)
    {
        $set = $this->graph->getEdgeSet();
        $this->assertCount($eCard, $set);
        foreach ($set as $item) {
            $this->assertInstanceOf('Trismegiste\Mondrian\Graph\Edge', $item);
        }

        $set = $this->graph->getVertexSet();
        $this->assertCount($vCard, $set);
        foreach ($set as $item) {
            $this->assertInstanceOf('Trismegiste\Mondrian\Graph\Vertex', $item);
        }
    }

    public function testCompleteGraph()
    {
        $card = 5;

        for ($k = 0; $k < $card; $k++) {
            $this->graph->addVertex(new Vertex($k));
        }
        foreach ($this->graph->getVertexSet() as $src) {
            foreach ($this->graph->getVertexSet() as $dst) {
                if ($src === $dst)
                    continue;
                $this->graph->addEdge($src, $dst);
            }
        }

        $this->checkSimpleGraph($card, $card * ($card - 1));
    }

    public function testSuccessorForNotFound()
    {
        $set = $this->graph->getSuccessor(new Vertex('A'));
        $this->assertNull($set);
    }

    public function testNoSuccessor()
    {
        $v = new Vertex('A');
        $this->graph->addVertex($v);
        $set = $this->graph->getSuccessor($v);
        $this->assertEquals(array(), $set);
    }

    public function testSuccessor()
    {
        $v1 = new Vertex('A');
        $v2 = new Vertex('B');
        $this->graph->addEdge($v1, $v2);
        $set = $this->graph->getSuccessor($v1);
        $this->assertEquals(array($v2), $set);
        $set = $this->graph->getSuccessor($v2);
        $this->assertEquals(array(), $set);
    }

}