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

    public function export()
    {
        $default = array('fixedsize' => true, 'width' => 2, 'height' => 2);
        $dot = new Digraph('G');
        $inverseIndex = new \SplObjectStorage();
        foreach ($this->graph->getVertexSet() as $idx => $vertex) {
            $inverseIndex[$vertex] = $idx;
            $dot->node($idx, array_merge($default, $vertex->getAttribute()));
        }
        foreach ($this->graph->getEdgeSet() as $edge) {
            $dot->edge(array($inverseIndex[$edge->getSource()], $inverseIndex[$edge->getTarget()]));
        }
        return $dot->render();
    }

}