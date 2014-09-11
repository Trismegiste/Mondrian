<?php

namespace Trismegiste\Mondrian\Tests\Transform\Format;

use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Transform\Vertex\ClassVertex;

/**
 * Fixture : non-planar digraph
 */
class NotPlanar extends Digraph
{

    public function __construct()
    {
        parent::__construct();
        for ($k = 0; $k < 5; $k++) {
            $set[] = new ClassVertex('Guess\What\I\Draw' . $k);
        }
        for ($k = 0; $k < 5; $k++) {
            $this->addEdge($set[(2 * $k) % 5], $set[(2 * $k + 2) % 5]);
        }
    }

    public function getPartition()
    {
        return array($this->getVertexSet());
    }

}
