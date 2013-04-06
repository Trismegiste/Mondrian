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
 * I mean between public methods without abstraction.
 *
 * How ?
 * This analyser searches path between two classes through calls of public
 * methods, inheritance or instanciation. 
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
 * with an attached data structure, not an abstract idea.
 *
 * Therefore, each time you make a modification in B::getThing(), you can
 * break its contract and break something in A::doThing(). Worst, A has
 * a link to B, therefore A can call anything in B. Classes get fat, instable,
 * and you fear each time you move a semi-colon.
 *
 * The language I used for representing source code into a digraph was
 * created especially to show that.
 * 
 * Note 1 : This service creates a new digraph by selecting only the class
 * vertices because with the implementations, there are too many vertices.
 * The goal of the digraph is the "search for bridges". This is a concept
 * in graph theory where two highly connected graphs are linked by only one
 * edge. By cuting this edge (by adding an interface for example), you can
 * easily break your "monolith of code" into two pieces.
 * 
 * Note 2 : since I only analyse public methods, I knowingly miss some 
 * connections. I state that it is not an issue now. If there is a new
 * instance in a protected method, this an "inner refactoring" not a refactoring
 * of the structure of public implementations. 
 * 
 * In a second time, you can refactor this coupling later because you have
 * more freedom to change that : you are in a class, there is no coupling outside,
 * or perhaps it's ok (factory method pattern for example). Remember, the purpose
 * of this service is to help you to "break a monolith" you barely know, 
 * not to replace your coding skills. There is no magic for that.
 * 
 * There are more immportant issues with cycles of components for example. 
 *
 */
class SpaghettiCoupling extends BreadthFirstSearch
{

    /**
     * Generate a digraph reduced to the concrete coupled methods
     */
    protected function generateGraph()
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
     * Generate a digraph reduced to all concrete coupled classes
     */
    public function generateCoupledClassGraph()
    {
        $reducedGraph = new \Trismegiste\Mondrian\Graph\Digraph();
        $vSet = $this->graph->getVertexSet();
        foreach ($vSet as $src) {
            if ($src instanceof ClassVertex) {
                foreach ($vSet as $dst) {
                    if (($dst instanceof ClassVertex) && ($dst !== $src)) {
                        $this->resetVisited();
                        $path = $this->searchPath($src, $dst);
                        // since I build an arborescence on class
                        // vertices, I stop on the first encountered class
                        foreach ($path as $step) {
                            if ($step->getTarget() instanceof ClassVertex) {
                                $reducedGraph->addEdge($src, $step->getTarget());
                                break;
                            }
                        }
                    }
                }
            }
        }
        return $reducedGraph;
    }

    protected function generateCoupledClassGraph2()
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