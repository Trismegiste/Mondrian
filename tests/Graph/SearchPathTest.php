<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Graph;

use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Graph\Vertex;
use Trismegiste\Mondrian\Graph\Edge;

/**
 * SearchPathTest is a template method for testing path search algorithm
 */
abstract class SearchPathTest extends \PHPUnit\Framework\TestCase
{

    protected $graph;

    protected function setUp(): void
    {
        $this->graph = $this->createGraph(new Digraph());
    }

    abstract protected function createGraph(Digraph $g);

    protected function tearDown(): void
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
        $vCard = 20; // plus one (see the line below)
        for ($k = 0; $k <= $vCard; $k++) {
            $this->graph->addVertex(new Vertex($k));
        }
        $vSet = $this->graph->getVertexSet();
        // what a funny use of a for loop : a "non-deterministic for" (^o^)
        for ($src = $dst = 0; $src != $vCard; $dst = rand(0, $vCard)) {
            if ($src != $dst) {
                $this->graph->addEdge($vSet[$src], $vSet[$dst]);
                $src = $dst;
            }
        }

        $path = $this->graph->searchPath($vSet[0], $vSet[$vCard]);
        $this->assertGreaterThan(0, count($path));
    }

    protected function recursivAppendTree($level, $prefix = '>')
    {
        $node = new Vertex($prefix);
        if ($level > 0) {
            foreach (array('L', 'R') as $branch) {
                $this->graph->addEdge($node, $this->recursivAppendTree($level - 1, $prefix . $branch));
            }
        }
        return $node;
    }

    public function testBinary()
    {
        $level = 6;
        $root = $this->recursivAppendTree($level);
        $vCard = (2 << $level) - 1;
        $this->assertCount($vCard, $this->graph->getVertexSet());

        $path = $this->graph->searchPath($root, $this->findVertexByName('>LRLRLR'));
        $this->assertCount($level, $path);
    }

    public function testNoPath()
    {
        $this->recursivAppendTree(6);

        $path = $this->graph->searchPath($this->findVertexByName('>L'), $this->findVertexByName('>RRRRRR'));
        $this->assertCount(0, $path);
    }

    protected function findVertexByName($name)
    {
        $vSet = $this->graph->getVertexSet();
        foreach ($vSet as $v) {
            if ($v->getName() == $name) {
                return $v;
            }
        }
    }

}
