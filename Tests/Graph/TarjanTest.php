<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Graph;

use Trismegiste\Mondrian\Graph\Tarjan;
use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Graph\Vertex;

/**
 * TarjanTest is a test class for Tarjan algorithm
 *
 * @author florent
 */
class TarjanTest extends GraphTest
{

    protected function createGraphInstance()
    {
        return new Tarjan(new Digraph());
    }

    public function testAlgo()
    {
        for ($k = 0; $k < 6; $k++) {
            $this->graph->addVertex(new Vertex($k));
        }

        $vertex = $this->graph->getVertexSet();

        for ($offset = 0; $offset < 6; $offset += 3) {
            for ($k = 0; $k < 3; $k++) {
                $this->graph->addEdge($vertex[$offset + $k], $vertex[$offset + (($k + 1) % 3)]);
            }
        }

        $ret = $this->graph->getStronglyConnected();
        $this->assertCount(2, $ret, 'Two cycles');
        $this->assertCount(3, $ret[0]);
        $this->assertCount(3, $ret[1]);
    }

    public function testCompleteGraph()
    {
        $card = 6;

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

        $ret = $this->graph->getStronglyConnected();

        $this->assertCount(1, $ret, 'One SCC');
        $this->assertCount($card, $ret[0]);
    }

    public function testNotObviousGraph()
    {
        $cls = new Vertex('class');
        $meth = new Vertex('method');
        $param = new Vertex('param');
        $impl = new Vertex('impl');

        $this->graph->addEdge($cls, $meth);
        $this->graph->addEdge($meth, $impl);
        $this->graph->addEdge($impl, $cls);
        $this->graph->addEdge($impl, $param);
        $this->graph->addEdge($meth, $param);

        $ret = $this->graph->getStronglyConnected();
        $this->assertCount(2, $ret);
        $this->assertCount(1, $ret[0]);
        $this->assertCount(3, $ret[1]);
    }

}