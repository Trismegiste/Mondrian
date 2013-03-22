<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Graph;

use Trismegiste\Mondrian\Graph\Digraph;

/**
 * DigraphTest is a test for Digraph
 */
class DigraphTest extends GraphTest
{

    protected function createGraphInstance()
    {
        return new Digraph();
    }

}