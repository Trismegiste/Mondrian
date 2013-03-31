<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Graph;

use Trismegiste\Mondrian\Graph\DepthFirstSearch;
use Trismegiste\Mondrian\Graph\Digraph;
use Trismegiste\Mondrian\Graph\Vertex;
use Trismegiste\Mondrian\Graph\Edge;

/**
 * DepthFirstSearchTest is ...
 *
 * @author flo
 */
class DepthFirstSearchTest extends SearchPathTest
{

    protected function createGraph(Digraph $g)
    {
        return new DepthFirstSearch($g);
    }

}