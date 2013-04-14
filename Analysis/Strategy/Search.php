<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Analysis\Strategy;

use Trismegiste\Mondrian\Graph\Vertex;
use Trismegiste\Mondrian\Graph\Edge;

/**
 * Search is a contract for searching coupled component
 */
interface Search
{

    /**
     * Add or not a shortcut from $src to $dst by following $path
     */
    function collapseEdge(Vertex $src, Vertex $dst, array $path);
}