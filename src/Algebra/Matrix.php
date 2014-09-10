<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Algebra;

/**
 * A 2D matrix
 */
interface Matrix
{

    /**
     * get the size of this matrix
     *
     * @return int
     */
    function getSize();

    /**
     * Get  coefficient in this matrix
     * Use the algebra order line x column
     *
     * @param int $line
     * @param int $column
     *
     * @return numeric
     */
    function get($line, $column);

    /**
     * Set a coefficient in this matrix
     *
     * @param int $line
     * @param int $column
     * @param numeric $value
     */
    function set($line, $column, $value);
}
