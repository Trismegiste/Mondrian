<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform\Format;

use Alom\Graphviz\Digraph;
use Trismegiste\Mondrian\Graph\Vertex;

/**
 * Graphviz is a decorator for GraphViz output
 *
 */
class Graphviz extends GraphExporter
{

    protected function createGraphVizDot()
    {
        return new Digraph('PhpGraph');
    }

    public function export()
    {
        $default = array('fixedsize' => true, 'width' => 2, 'height' => 2);
        $dot = $this->createGraphVizDot();
        $inverseIndex = new \SplObjectStorage();
        // add vertices
        foreach ($this->graph->getVertexSet() as $idx => $vertex) {
            $inverseIndex[$vertex] = $idx;
            $dot->node($idx, array_merge($default, $vertex->getAttribute()));
        }
        // add edges
        foreach ($this->graph->getEdgeSet() as $edge) {
            $dot->edge(array($inverseIndex[$edge->getSource()], $inverseIndex[$edge->getTarget()]));
        }
        // add cluster
        foreach ($this->getPartition() as $idx => $subgraph) {
            $scc = $dot->subgraph("cluster_$idx");
            $scc->attr('graph', array('bgcolor' => 'antiquewhite'));
            foreach ($subgraph as $vertex) {
                $scc->node($inverseIndex[$vertex]);
            }
        }

        return $dot->render();
    }

}
