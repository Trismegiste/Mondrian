<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Analysis;

use Trismegiste\Mondrian\Graph\BreadthFirstSearch;
use Trismegiste\Mondrian\Transform\Vertex\ImplVertex;
use Trismegiste\Mondrian\Transform\Vertex\MethodVertex;
use Trismegiste\Mondrian\Graph\Edge;

/**
 * HiddenCoupling is an analyser which checks and finds hidden coupling
 * between types
 */
class HiddenCoupling extends BreadthFirstSearch
{

    public function generateGraph()
    {
        $dependency = $this->getEdgeSet();
        foreach ($dependency as $edge) {
            if (($edge->getSource() instanceof ImplVertex)
                    && ($edge->getTarget() instanceof MethodVertex)) {
                $this->resetSearch();
                $otherPath = $this->findOtherPath($edge);
                if (count($otherPath) == 0) {
                    // not found => hidden coupling
                    print_r($edge);
                }
            }
        }
    }

    protected function resetSearch()
    {
        $this->stack = array();
        foreach ($this->getEdgeSet() as $e) {
            unset($e->visited);
        }
    }

    protected function findOtherPath(Edge $dep)
    {
        $dep->visited = true;
        $start = new \SplObjectStorage();
        $step = $this->graph->getEdgeIterator($dep->getSource());
        foreach ($step as $edge) {
            if ($edge != $dep) {
                $start[$step->getInfo()] = null;
            }
        }

        $e = $this->recursivSearchPath($start, $dep->getTarget());
        if (!is_null($e)) {
            array_unshift($this->stack, $e);
        }

        return $this->stack;
    }

}