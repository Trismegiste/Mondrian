<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Transform\Vertex;

use Trismegiste\Mondrian\Transform\Vertex\InterfaceVertex;

/**
 * InterfaceVertexTest is an interface vertex
 */
class InterfaceVertexTest extends \PHPUnit_Framework_TestCase
{

    protected $vertex;

    protected function setUp()
    {
        $this->vertex = new InterfaceVertex('SessionHandlerInterface');
    }

    public function testCompactLabel()
    {
        $attr = $this->vertex->getAttribute();
        $this->assertEquals('SessionHandlerInterface', $attr['label']);
    }

}