<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Graph;

use Trismegiste\Mondrian\Graph\Algorithm;
use Trismegiste\Mondrian\Graph\Digraph;

/**
 * AlgorithmTest is a test for Digraph
 */
class AlgorithmTest extends GraphTest
{

    protected function createGraphInstance()
    {
        return new Algorithm(new Digraph());
    }

}