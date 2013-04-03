<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Algebra;

/**
 * ByteMatrix is a matrix of unsigned byte
 *
 * @author florent
 */
class ByteMatrix
{

    const CHAR_PER_COEFF = 2;

    protected $dimension;
    // 8 bit each = 2 char
    protected $content;

    public function __construct($dimension)
    {
        $this->content = &str_repeat(0, self::CHAR_PER_COEFF * $dimension * $dimension);
        $this->dimension = $dimension;
    }

    public function getSize()
    {
        return $this->dimension;
    }

    /**
     * Use algebra order
     */
    public function get($line, $column)
    {
        return hexdec(substr($this->content, self::CHAR_PER_COEFF * ($line * $this->dimension + $column), self::CHAR_PER_COEFF));
    }

    public function set($line, $column, $value)
    {
        $delta = self::CHAR_PER_COEFF * ($line * $this->dimension + $column);
        $hex = sprintf('%02x', $value);
        for ($i = 0; $i < self::CHAR_PER_COEFF; $i++) {
            $this->content[$delta + $i] = $hex[$i];
        }
    }

}