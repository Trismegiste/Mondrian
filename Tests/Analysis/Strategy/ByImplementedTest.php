<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Analysis\Strategy;

use Trismegiste\Mondrian\Analysis\Strategy\ByImplemented;
use Trismegiste\Mondrian\Transform\Vertex\ClassVertex;
use Trismegiste\Mondrian\Transform\Vertex\ImplVertex;
use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Graph\Edge;
use Trismegiste\Mondrian\Graph\Vertex;

/**
 * ByImplementedTest is a unit test for ByConnection strategy
 */
class ByImplementedTest extends TestTemplate
{

    protected function createStrategy(Digraph $g)
    {
        return new ByImplemented($g);
    }

    /**
     * @dataProvider getPath
     */
    public function testDirect($src, $dst)
    {
        $this->strategy->collapseEdge($src, $dst, array(new Edge($src, $dst)));
        $this->assertCount(0, $this->result->getVertexSet());
    }

    /**
     * @dataProvider getPath
     */
    public function testIndirect($src, $dst)
    {
        $inter = new ImplVertex('C');
        $path = $this->buildPath($src, $inter, $dst);
        $this->strategy->collapseEdge($src, $dst, $path);
        $this->assertCount(2, $this->result->getVertexSet());
        $edgeList = $this->result->getEdgeSet();
        $this->assertCount(1, $edgeList);
        $this->assertEquals($src, $edgeList[0]->getSource());
        $this->assertEquals($dst, $edgeList[0]->getTarget());
    }

    /**
     * @dataProvider getPath
     */
    public function testNoLinkByImpl($src, $dst)
    {
        $path = $this->buildPath($src, new ClassVertex('C'), new ImplVertex('impl'), $dst);
        $this->strategy->collapseEdge($src, $dst, $path);
        $this->assertCount(0, $this->result->getVertexSet());
    }

    /**
     * @dataProvider getPath
     */
    public function testArborescenceCut($src, $dst)
    {
        $impl = new ImplVertex('impl');
        $inter = new ClassVertex('C');
        $path = $this->buildPath($src, $impl, $inter, $dst);
        $this->strategy->collapseEdge($src, $dst, $path);
        $this->assertCount(2, $this->result->getVertexSet());
        $edgeList = $this->result->getEdgeSet();
        $this->assertCount(1, $edgeList);
        $this->assertEquals($src, $edgeList[0]->getSource());
        $this->assertEquals($inter, $edgeList[0]->getTarget(), 'Stop at the first encountered class');
    }

}