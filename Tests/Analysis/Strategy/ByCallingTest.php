<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Analysis\Strategy;

use Trismegiste\Mondrian\Analysis\Strategy\ByCalling;
use Trismegiste\Mondrian\Transform\Vertex\ClassVertex;
use Trismegiste\Mondrian\Transform\Vertex\ImplVertex;
use Trismegiste\Mondrian\Transform\Vertex\MethodVertex;
use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Graph\Edge;
use Trismegiste\Mondrian\Graph\Vertex;

/**
 * ByCallingTest is a unit test for ByCalling strategy
 */
class ByCallingTest extends TestTemplate
{

    protected function createStrategy(Digraph $g)
    {
        return new ByCalling($g);
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
        $path = $this->buildPath($src, new ImplVertex('implA'), new MethodVertex('implB'), new ImplVertex('implB'), $dst);
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
    public function testNoLinkByCalling($src, $dst)
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
        $inter = new ClassVertex('C');
        $path = $this->buildPath($src, new ImplVertex('implA'), new MethodVertex('implB'), new ImplVertex('implC'), $inter, $dst);
        $this->strategy->collapseEdge($src, $dst, $path);
        $this->assertCount(2, $this->result->getVertexSet());
        $edgeList = $this->result->getEdgeSet();
        $this->assertCount(1, $edgeList);
        $this->assertEquals($src, $edgeList[0]->getSource());
        $this->assertEquals($inter, $edgeList[0]->getTarget(), 'Stop at the first encountered class');
    }

}