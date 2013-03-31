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
 * 
 * How ? 
 * This analyser searches for method calls. Everytime there is a call of
 * a method against an object ( $obj->getThing() ), it means an edge from
 * an implementation vertex to a method signature vertex.
 * 
 * Since "$obj" does not come from nowhere, its type (class or interface)
 * must be known by the class owning the implementation vertex. 
 * That's why : 
 * If there an edge from an implementation to a method, there must be
 * at least one another directed path between these two vertices 
 * (through the class vertex, through a parameter vertex, superclass etc...)
 * 
 * If there is none, *maybe* it means a hidden coupling. I add the "maybe"
 * because, it's hard to find the type of "$obj" in soft-typed language like
 * PHP. That's why there can be false positive. But it's easier to check
 * false positives than to search through all over the php files to find
 * that kind of weakness in the code.
 * 
 * One another thing, since I cannot detect calls from "call_user_func" and
 * other magic features of PHP like "$obj->$methodName()" or "new $className"
 * there is a big limit of this analyser. 
 * 
 * Neverthesless I pretend this tool can find about 50% of hidden coupling
 * in poorly-coded classes.
 * 
 */
class HiddenCoupling extends BreadthFirstSearch
{

    /**
     * Generate a digraph reduced to the hidden coupled vertices
     */
    public function generateGraph()
    {
        $dependency = $this->getEdgeSet();
        foreach ($dependency as $edge) {
            if (($edge->getSource() instanceof ImplVertex)
                    && ($edge->getTarget() instanceof MethodVertex)) {
//                printf("checking %s -> %s = ", $edge->getSource()->getName(), $edge->getTarget()->getName());
                $this->resetSearch();
                $otherPath = $this->findOtherPath($edge);
//                printf("%d\n", count($otherPath));
                if (count($otherPath) == 0) {
                    // not found => hidden coupling
                    printf("%s -> %s \n", $edge->getSource()->getName(), $edge->getTarget()->getName());
                }
            }
        }
    }

    /**
     * Reset the algorithm (visited edge and stack of path)
     */
    protected function resetSearch()
    {
        $this->stack = array();
        foreach ($this->getEdgeSet() as $e) {
            unset($e->visited);
        }
    }

    /**
     * Get another path which starts from $dep->getSource() and ends to
     * $dep->getTarget()
     * 
     * @param Edge $dep the direct directed path on which we work
     * @return Edge[]
     */
    protected function findOtherPath(Edge $dep)
    {
        // set the edge as visited
        $dep->visited = true;
        // make a set of edges to start the exploration by excluding
        // the initial edge we already known
        $start = new \SplObjectStorage();
        $step = $this->graph->getEdgeIterator($dep->getSource());
        foreach ($step as $edge) {
            // exclude the direct path
            if ($step->getInfo() != $dep) {
                $start[$step->getInfo()] = null;
            }
        }

        // launching the BFS algo
        $e = $this->recursivSearchPath($start, $dep->getTarget());
        if (!is_null($e)) {
            array_unshift($this->stack, $e);
        }

        return $this->stack;
    }

}