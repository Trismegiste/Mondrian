<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Analysis;

use Trismegiste\Mondrian\Transform\Vertex\ImplVertex;
use Trismegiste\Mondrian\Transform\Vertex\MethodVertex;
use Trismegiste\Mondrian\Transform\Vertex\ClassVertex;
use Trismegiste\Mondrian\Transform\Vertex\InterfaceVertex;
use Trismegiste\Mondrian\Graph\Edge;
use Trismegiste\Mondrian\Graph\BreadthFirstSearch;

/**
 * SpaghettiCoupling is an analyser which finds coupling between implementations,
 * I mean between methods without abstraction.
 *
 * How ?
 * This analyser searches path between two method implementations through
 * their calls.
 *
 * Example :
 * In the implementation of the method A::doThing(), there is a call to
 * the method B::getThing().
 *
 * If B::getThing() is declared in B, the two methods are coupled. One can
 * find a directed path between these implementation vertices.
 *
 * If B::getThing() is an implementation of C::getThing() declared in the C interface
 * from which B inherits, there is no coupling because, A::doThing() is linked
 * to C::getThing(), therefore no directed path. Liskov principle is safe.
 *
 * The first case is what I call "modern spaghetti code" :
 * yes you haZ objects and classes
 * but you are not S.O.L.I.D. You rely on concrete class, not abstraction,
 * not "contract" (interface). Your classes are just a collection of functions
 * with an attached data structure, not an abstract concept.
 *
 * Therefore, each time you make a modification in B::getThing(), you can
 * break its contract and break something in A::doThing(). Worst, A has
 * a link to B, therefore A can call anything in B. Classes get fat, instable,
 * and you fear each time you move a semi-colon.
 *
 * The language I used for representing source code into a digraph was
 * created especially to show that.
 *
 */
class SpaghettiCoupling extends BreadthFirstSearch
{

    /**
     * Generate a digraph reduced to the concrete coupled methods
     */
    public function generateGraph()
    {
        $reducedGraph = new \Trismegiste\Mondrian\Graph\Digraph();
        $eSet = $this->graph->getEdgeSet();
        foreach ($eSet as $edge) {
            if (($edge->getSource() instanceof ImplVertex)
                    && ($edge->getTarget() instanceof MethodVertex)) {
                $impl = $edge->getSource();
                $called = $edge->getTarget();
                foreach ($this->graph->getSuccessor($called) as $dst) {
                    if ($dst instanceof ImplVertex) {
                        $reducedGraph->addEdge($impl, $called);
                        $reducedGraph->addEdge($called, $dst);
                    }
                }
            }
        }

        return $reducedGraph;
    }

    /**
     * Generate a digraph reduced to all concrete coupled methods
     */
    public function generateCoupledImplGraph()
    {
        $reducedGraph = new \Trismegiste\Mondrian\Graph\Digraph();
        $vSet = $this->graph->getVertexSet();
        foreach ($vSet as $src) {
            if ($src instanceof ImplVertex) {
                foreach ($vSet as $dst) {
                    if (($dst instanceof ImplVertex) && ($dst !== $src)) {
                        $this->resetVisited();
                        $path = $this->searchPath($src, $dst);
                        foreach ($path as $edge) {
                            $reducedGraph->addEdge($edge->getSource(), $edge->getTarget());
                        }
                    }
                }
            }
        }
        return $reducedGraph;
    }

    public function generateCoupledClassGraph()
    {
        $reducedGraph = new \Trismegiste\Mondrian\Graph\Digraph();
        $vSet = $this->graph->getVertexSet();
        $topo = new \Trismegiste\Mondrian\Graph\FloydWarshall($this->graph);

        $matrix = $topo->getDistance();
        foreach ($vSet as $line => $src) {
            if ($src instanceof ClassVertex) {
                foreach ($vSet as $column => $dst) {
                    if ($dst instanceof ClassVertex) {
                        if ($matrix->get($line, $column) > 0) {
                            $reducedGraph->addEdge($src, $dst);
                        }
                    }
                }
            }
        }

        return $reducedGraph;
    }

}