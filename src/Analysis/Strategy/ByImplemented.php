<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Analysis\Strategy;

use Trismegiste\Mondrian\Graph\Vertex;
use Trismegiste\Mondrian\Graph\Edge;
use Trismegiste\Mondrian\Graph\Graph;
use Trismegiste\Mondrian\Transform\Vertex\ImplVertex;
use Trismegiste\Mondrian\Transform\Vertex\MethodVertex;
use Trismegiste\Mondrian\Transform\Vertex\ClassVertex;

/**
 * ByImplemented selects path between classes if
 * there is a use in a implementation. It stops at the first class
 * because connect property is transitive.
 */
class ByImplemented implements Search
{

    protected $reducedGraph;

    public function __construct(Graph $g)
    {
        $this->reducedGraph = $g;
    }

    public function collapseEdge(Vertex $src, Vertex $dst, array $path)
    {
        // checking if the path go through on implementation
        $implFound = false;
        foreach ($path as $step) {
            if ($step->getTarget() instanceof ClassVertex) {
                break;
            }
            if ($step->getTarget() instanceof ImplVertex) {
                $implFound = true;
                break;
            }
        }
        // since I build an arborescence on class
        // vertices, I stop on the first encountered class
        if ($implFound) {
            foreach ($path as $step) {
                if ($step->getTarget() instanceof ClassVertex) {
                    $this->reducedGraph->addEdge($src, $step->getTarget());
                    break;
                }
            }
        }
    }

}
