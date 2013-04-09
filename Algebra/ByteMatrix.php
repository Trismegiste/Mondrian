<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Algebra;

/**
 * ByteMatrix is a compressed matrix of unsigned shortint
 *
 * @author florent
 */
class ByteMatrix implements Matrix
{

    const CHAR_PER_COEFF = 4;

    protected $dimension;
    protected $content;

    /**
     * build a square matrix
     *
     * @param int $dimension size of matrix
     */
    public function __construct($dimension)
    {
        $this->content = str_repeat(0, self::CHAR_PER_COEFF * $dimension * $dimension);
        $this->dimension = $dimension;
    }

    /**
     * {@inheritDoc}
     */
    public function getSize()
    {
        return $this->dimension;
    }

    /**
     * {@inheritDoc}
     */
    public function get($line, $column)
    {
        return hexdec(substr($this->content, self::CHAR_PER_COEFF * ($line * $this->dimension + $column), self::CHAR_PER_COEFF));
    }

    /**
     * {@inheritDoc}
     */
    public function set($line, $column, $value)
    {
        $delta = self::CHAR_PER_COEFF * ($line * $this->dimension + $column);
        $hex = sprintf('%0' . self::CHAR_PER_COEFF . 'x', $value);
        for ($i = 0; $i < self::CHAR_PER_COEFF; $i++) {
            $this->content[$delta + $i] = $hex[$i];
        }
    }

}