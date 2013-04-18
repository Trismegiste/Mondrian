<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Analysis;

use Trismegiste\Mondrian\Graph\Graph;

/**
 * Contract for adding information on vertices in a Graph
 */
interface VertexDecorator
{

    /**
     * Add metadata to vertices
     */
    function decorate();
}
