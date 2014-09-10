<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Analysis\Strategy;

use Trismegiste\Mondrian\Analysis\Strategy\ByConnection;
use Trismegiste\Mondrian\Transform\Vertex\ClassVertex;
use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Graph\Edge;
use Trismegiste\Mondrian\Graph\Vertex;

/**
 * ByConnectionTest is a unit test for ByConnection strategy
 */
class ByConnectionTest extends TestTemplate
{

    protected function createStrategy(Digraph $g)
    {
        return new ByConnection($g);
    }

    /**
     * @dataProvider getPath
     */
    public function testDirect($src, $dst)
    {
        $this->strategy->collapseEdge($src, $dst, array(new Edge($src, $dst)));
        $this->assertCount(2, $this->result->getVertexSet());
        $this->assertCount(1, $this->result->getEdgeSet());
    }

    /**
     * @dataProvider getPath
     */
    public function testIndirect($src, $dst)
    {
        $inter = new Vertex('C');
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
    public function testArborescence($src, $dst)
    {
        $inter = new ClassVertex('C');
        $path = $this->buildPath($src, $inter, $dst);
        $this->strategy->collapseEdge($src, $dst, $path);
        $this->assertCount(2, $this->result->getVertexSet());
        $edgeList = $this->result->getEdgeSet();
        $this->assertCount(1, $edgeList);
        $this->assertEquals($src, $edgeList[0]->getSource());
        $this->assertEquals($inter, $edgeList[0]->getTarget(), 'Stop at the first encountered class');
    }

}
