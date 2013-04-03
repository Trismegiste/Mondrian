<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Algebra;

use Trismegiste\Mondrian\Algebra\ByteMatrix;

/**
 * ByteMatrixTest is a ...
 *
 * @author florent
 */
class ByteMatrixTest extends \PHPUnit_Framework_TestCase
{

    protected $matrix;

    protected function setUp()
    {
        $this->matrix = new ByteMatrix(3);
    }

    public function testZero()
    {
        $dim = $this->matrix->getSize();
        for ($y = 0; $y < $dim; $y++) {
            for ($x = 0; $x < $dim; $x++) {
                $this->assertEquals(0, $this->matrix->get($y, $x));
            }
        }
    }

    public function testSetterGetter()
    {
        $dim = $this->matrix->getSize();
        for ($y = 0; $y < $dim; $y++) {
            for ($x = 0; $x < $dim; $x++) {
                $value = ($x + $y) % 256;
                $this->matrix->set($y, $x, $value);
            }
        }

        for ($y = 0; $y < $dim; $y++) {
            for ($x = 0; $x < $dim; $x++) {
                $value = ($x + $y) % 256;
                $this->assertEquals($value, $this->matrix->get($y, $x));
            }
        }
    }

}