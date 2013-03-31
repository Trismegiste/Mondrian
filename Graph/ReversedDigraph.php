<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Graph;

/**
 * ReversedDigraph is a decorator which provides a reversed
 * graph of this directed graph
 */
class ReversedDigraph extends Algorithm
{

    /**
     * Build the reversed digraph of this digraph
     * 
     * @return Digraph 
     */
    public function getReversed()
    {
        $newGraph = new Digraph();
        foreach ($this->graph->getVertexSet() as $vx) {
            // for isolated vertex :
            $newGraph->addVertex($vx);
            // we reverse each edge :
            foreach ($this->graph->getSuccessor($vx) as $vy) {
                $newGraph->addEdge($vy, $vx);
            }
        }

        return $newGraph;
    }

}