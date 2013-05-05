<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Analysis;

use Trismegiste\Mondrian\Graph\Algorithm;
use Trismegiste\Mondrian\Transform\Vertex\InterfaceVertex;
use Trismegiste\Mondrian\Transform\Vertex\MethodVertex;
use Trismegiste\Mondrian\Transform\Vertex\ParamVertex;
use Trismegiste\Mondrian\Transform\Vertex\ClassVertex;

/**
 * CouplingMaker searches for interface with class in its methods parameters.
 * 
 * This is bad because each time you inherit from these interface, you create
 * coupling between concrete classes and god kills a kitten.
 * 
 * That's why these interfaces are literally "coupling generators", this is
 * a seed for spaghetti coupling. 
 */
class CouplingMaker extends Algorithm implements Generator
{

    public function createReducedGraph()
    {
        $reduced = new \Trismegiste\Mondrian\Graph\Digraph();
        foreach ($this->getEdgeSet() as $declaring) {
            // we search for methods declared in interfaces
            if (($declaring->getSource() instanceof InterfaceVertex)
                    && ($declaring->getTarget() instanceof MethodVertex)) {
                $method = $declaring->getTarget();
                // scan for all parameters
                foreach ($this->getEdgeIterator($method) as $param) {
                    if ($param instanceof ParamVertex) {
                        // we find a param, we scan for type hint
                        foreach ($this->getEdgeIterator($param) as $typeHint) {
                            if ($typeHint instanceof ClassVertex) {
                                // we find a typed parameter with a class : evil                                
                                // we add the shortcut path (skip the parameter, not relevant)
                                $reduced->addEdge($declaring->getSource(), $method);
                                $reduced->addEdge($method, $typeHint);
                            }
                        }
                    }
                }
            }
        } // I love this ^o^

        return $reduced;
    }

}