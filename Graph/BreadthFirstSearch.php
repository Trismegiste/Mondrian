<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Graph;

/**
 * BreadthFirstSearch is ...
 *
 * @author flo
 */
class BreadthFirstSearch extends Algorithm
{

    protected $stack = array();
    protected $excludedEdge = array();

    public function searchPath(Vertex $src, Vertex $dst)
    {
        $this->stack = array();
        $start = new \SplObjectStorage();
        $step = $this->graph->getEdgeIterator($src);
        foreach ($step as $e) {
            $start[$step->getInfo()] = null;
        }
        $e = $this->recursivSearchPath($start, $dst);
        if (!is_null($e)) {
            array_unshift($this->stack, $e);
        }
        return $this->stack;
    }

    protected function recursivSearchPath(\SplObjectStorage $step, Vertex $dst)
    {
        $nextLevel = new \SplObjectStorage();
        foreach ($step as $e) {
            $edge->visited = true;
            if ($e->getTarget() == $dst) {
                return $e;
            }
            $choice = $this->graph->getEdgeIterator($e->getTarget());
            foreach ($choice as $succ) {
                $edge = $choice->getInfo();
                if (!isset($edge->visited)) {
                    $nextLevel[$edge] = $e;
                }
            }
        }

        if (count($nextLevel)) {
            $ret = $this->recursivSearchPath($nextLevel, $dst);
            if (!is_null($ret)) {
                array_unshift($this->stack, $ret);
                return $nextLevel[$ret];
            }
        }
    }

}