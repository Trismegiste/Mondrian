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
 * SpaghettiCoupling is an analyser which finds coupling between classes,
 *
 * How ?
 * This analyser searches path between two classes through calls of public
 * methods, inheritance or instanciation. 
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

    protected $strategy = null;

    /**
     * Set the strategy to reduce the graph by shortening a path between 2
     * components
     * 
     * @param Strategy\Search $strategy 
     */
    public function setFilterPath(Strategy\Search $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * Generate a digraph reduced to all concrete coupled classes
     */
    public function generateCoupledClassGraph()
    {
        if (is_null($this->strategy)) {
            throw new \LogicException('No defined strategy');
        }
        $vSet = $this->graph->getVertexSet();
        foreach ($vSet as $src) {
            if ($src instanceof ClassVertex) {
                foreach ($vSet as $dst) {
                    if (($dst instanceof ClassVertex) && ($dst !== $src)) {
                        $this->resetVisited();
                        $path = $this->searchPath($src, $dst);
                        $this->strategy->collapseEdge($src, $dst, $path);
                    }
                }
            }
        }
    }

    /*
    private function generateCoupledClassGraph2()
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
    }*/

}