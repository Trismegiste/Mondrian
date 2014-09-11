<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Analysis;

/**
 * Contract for adding information on vertices in a Graph
 */
interface VertexDecorator
{

    /**
     * Add metadata to vertices
     */
    public function decorate();
}
