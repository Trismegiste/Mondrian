<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Graph;

use Trismegiste\Mondrian\Graph\BreadthFirstSearch;
use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Graph\Vertex;
use Trismegiste\Mondrian\Graph\Edge;

/**
 * SearchPathTest is a template method for testing path search algorithm
 */
abstract class SearchPathTest extends \PHPUnit_Framework_TestCase
{

    protected $graph;

    protected function setUp()
    {
        $this->graph = $this->createGraph(new Digraph());
    }

    abstract protected function createGraph(Digraph $g);

    protected function tearDown()
    {
        unset($this->graph);
    }

    public function testSimple()
    {
        $v1 = new Vertex(1);
        $v2 = new Vertex(2);
        $this->graph->addEdge($v1, $v2);
        $path = $this->graph->searchPath($v1, $v2);
        $this->assertCount(1, $path);
        $this->assertInstanceOf('Trismegiste\Mondrian\Graph\Edge', $path[0]);
        $this->assertEquals(1, $path[0]->getSource()->getName());
        $this->assertEquals(2, $path[0]->getTarget()->getName());
    }

    public function testChain()
    {
        $card = 5;
        for ($k = 0; $k < $card; $k++) {
            $this->graph->addVertex(new Vertex($k));
        }
        $vSet = $this->graph->getVertexSet();
        foreach ($vSet as $v) {
            if (isset($last)) {
                $this->graph->addEdge($last, $v);
            }
            $last = $v;
        }

        $path = $this->graph->searchPath($vSet[0], $vSet[$card - 1]);
        $this->assertCount($card - 1, $path);
        foreach ($path as $idx => $step) {
            $this->assertInstanceOf('Trismegiste\Mondrian\Graph\Edge', $step);
            $this->assertEquals($vSet[$idx], $step->getSource());
            $this->assertEquals($vSet[$idx + 1], $step->getTarget());
        }
    }

    public function testRandom()
    {
        $vCard = 20;
        $eCard = $vCard * 4;
        for ($k = 0; $k < $vCard; $k++) {
            $this->graph->addVertex(new Vertex($k));
        }
        $vSet = $this->graph->getVertexSet();
        for ($k = 0; $k < $eCard; $k++) {
            $src = rand(0, $vCard - 1);
            $dst = rand(0, $vCard - 1);
            if ($src != $dst) {
                $this->graph->addEdge($vSet[$src], $vSet[$dst]);
            }
        }

        $path = $this->graph->searchPath($vSet[0], $vSet[$vCard - 1]);
        $this->assertGreaterThan(0, count($path), "You have no luck");
    }

    protected function recursivCreateTree($level, Vertex $parent)
    {
        if ($level > 0) {
            $left = new Vertex($parent->getName() . 'L');
            $right = new Vertex($parent->getName() . 'R');
            $this->graph->addEdge($parent, $left);
            $this->graph->addEdge($parent, $right);
            $this->recursivCreateTree($level - 1, $left);
            $this->recursivCreateTree($level - 1, $right);
        }
    }

    public function testBinary()
    {
        $level = 6;
        $root = new Vertex('>');
        $this->recursivCreateTree($level, $root);
        $vSet = $this->graph->getVertexSet();
        $vCard = (2 << $level) - 1;
        $this->assertCount($vCard, $vSet);

        $path = $this->graph->searchPath($root, $vSet[$vCard - 3]);
        $this->assertCount($level, $path);
    }

    public function testNoPath()
    {
        $level = 6;
        $root = new Vertex('>');
        $this->recursivCreateTree($level, $root);
        $vSet = $this->graph->getVertexSet();
        $vCard = (2 << $level) - 1;

        $path = $this->graph->searchPath($vSet[1], $vSet[$vCard - 1]);
        $this->assertCount(0, $path);
    }

}
