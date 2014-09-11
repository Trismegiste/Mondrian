<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform\Vertex;

/**
 * Contract for getting information to view this vertex as a node in GraphViz
 */
interface Vizable
{

    /**
     * Get an array of attributes
     *
     * @return array
     */
    public function getAttribute();
}
