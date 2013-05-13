<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Graph;

/**
 * DepthFirstSearch is a decorator for digraph to find a directed path between
 * two vertices.
 *
 * Uses the depth first search method : not always the shortest path
 */
class DepthFirstSearch extends Algorithm
{

    protected $stack = array();

    /**
     * Finds a directed path from $src to $dst
     *
     * @param Vertex $src starting point
     * @param Vertex $dst ending point
     * @return Edge[] the path or empty array
     */
    public function searchPath(Vertex $src, Vertex $dst)
    {
        $this->stack = array();
        $this->recursivSearchPath($src, $dst);

        return $this->stack;
    }

    /**
     * Recursive search
     *
     * @param Vertex $src
     * @param Vertex $dst
     * @return boolean
     */
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
