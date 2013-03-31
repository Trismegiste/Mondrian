<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Graph;

use Trismegiste\Mondrian\Graph\DepthFirstSearch;
use Trismegiste\Mondrian\Graph\Digraph;

/**
 * DepthFirstSearchTest is a unit test for DFS algo
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