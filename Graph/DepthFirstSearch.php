<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Graph;

/**
 * DepthFirstSearch is ...
 *
 * @author flo
 */
class DepthFirstSearch extends Algorithm
{

    protected $stack = array();
    protected $excludedEdge = array();

    public function searchPath(Vertex $src, Vertex $dst)
    {
        $this->stack = array();
        $this->recursivSearchPath($src, $dst);

        return $this->stack;
    }

    protected function recursivSearchPath(Vertex $src, Vertex $dst)
    {
        $choice = $this->graph->getEdgeIterator($src);
        foreach ($choice as $succ) {
            $edge = $choice->getInfo();
            if (isset($edge->visited)) {
                continue;
            }
            array_push($this->stack, $edge);
            $edge->visited = true;
            if (($edge->getTarget() == $dst)
                    || ($this->recursivSearchPath($edge->getTarget(), $dst))) {
                return true;
            }
            array_pop($this->stack);
        }

        return false;
    }

}