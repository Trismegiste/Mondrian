<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform;

use Alom\Graphviz\Digraph;
use Trismegiste\Mondrian\Graph\Algorithm;
use Trismegiste\Mondrian\Graph\Vertex;

/**
 * Graphviz is a decorator for GraphViz output
 *
 */
class Graphviz extends Algorithm
{

    private function getIndex(Vertex $vertex)
    {
        preg_match('#([^\\\\]+)$#', get_class($vertex), $capt);
        $type = $capt[1];
        $filtered = preg_replace('#([^A-Za-z0-9])#', '_', $vertex->getName());
        return $type . $filtered;
    }

    public function getDot()
    {
        $default = array('fixedsize' => true, 'width' => 2, 'height' => 2);
        $dot = new Digraph('G');
        foreach ($this->graph->getVertexSet() as $vertex) {
            $dot->node($this->getIndex($vertex), array_merge($default, $vertex->getAttribute()));
        }
        foreach ($this->graph->getEdgeSet() as $edge) {
            $dot->edge(array($this->getIndex($edge->getSource()), $this->getIndex($edge->getTarget())));
        }
        return $dot->render();
    }

}