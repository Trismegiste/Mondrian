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
 * ByConnection selects shortcut on a path at the first class
 * because connect property is transitive.
 */
class ByConnection implements Search
{

    protected $reducedGraph;

    public function __construct(Graph $g)
    {
        $this->reducedGraph = $g;
    }

    public function collapseEdge(Vertex $src, Vertex $dst, array $path)
    {
        // since I build an arborescence on class
        // vertices, I stop on the first encountered class
        foreach ($path as $step) {
            if ($step->getTarget() instanceof ClassVertex) {
                $this->reducedGraph->addEdge($src, $step->getTarget());
                break;
            }
        }
    }

}
