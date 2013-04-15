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
 * ByCalling  selects path between classes if there is a call.
 * It stops at the first class because connect property is transitive.
 *
 */
class ByCalling implements Search
{

    protected $reducedGraph;

    public function __construct(Graph $g)
    {
        $this->reducedGraph = $g;
    }

    public function collapseEdge(Vertex $src, Vertex $dst, array $path)
    {
        // checking if the path go through a call before finding a class
        $callFound = false;
        foreach ($path as $step) {
            if ($step->getTarget() instanceof ClassVertex) {
                if ($callFound) {
                    // since I build an arborescence on class
                    // vertices, I stop on the first encountered class
                    $this->reducedGraph->addEdge($src, $step->getTarget());
                }
                break;
            }
            if (($step->getSource() instanceof ImplVertex) && ($step->getTarget() instanceof MethodVertex)) {
                $callFound = true;
            }
        }
    }

}