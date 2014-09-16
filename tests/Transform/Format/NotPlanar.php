<?php

namespace Trismegiste\Mondrian\Tests\Transform\Format;

use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Transform\Vertex\ClassVertex;
use Trismegiste\Mondrian\Transform\Vertex\TraitVertex;
use Trismegiste\Mondrian\Transform\Vertex\ImplVertex;
use Trismegiste\Mondrian\Transform\Vertex\MethodVertex;
use Trismegiste\Mondrian\Transform\Vertex\ParamVertex;

/**
 * Fixture : non-planar digraph
 */
class NotPlanar extends Digraph
{

    public function __construct()
    {
        parent::__construct();

        $set = [
            new ClassVertex('Guess\What\I\Draw'),
            new TraitVertex('Guess\What\I\Draw'),
            new ImplVertex('Guess\What\I\Draw::yop'),
            new MethodVertex('Guess\What\I\Draw::yop'),
            new ParamVertex('Guess\What\I\Draw::yop/0'),
        ];

        for ($k = 0; $k < 5; $k++) {
            $this->addEdge($set[(2 * $k) % 5], $set[(2 * $k + 2) % 5]);
        }
    }

    public function getPartition()
    {
        return array($this->getVertexSet());
    }

}
