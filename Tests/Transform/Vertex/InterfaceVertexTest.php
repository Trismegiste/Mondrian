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

    public function testCompactLabel()
    {
        $vertex = new InterfaceVertex('SessionHandlerInterface');
        $attr = $vertex->getAttribute();
        $this->assertEquals('SessionHandlerInterface', $attr['label']);
    }

}