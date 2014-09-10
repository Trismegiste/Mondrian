<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Transform\Vertex;

use Trismegiste\Mondrian\Transform\Vertex\InterfaceVertex;
use Trismegiste\Mondrian\Transform\Vertex\StaticAnalysis;

/**
 * InterfaceVertexTest is an interface vertex
 */
class InterfaceVertexTest extends \PHPUnit_Framework_TestCase
{

    public function testCompactLabelWithoutNS()
    {
        $vertex = new InterfaceVertex('SessionHandlerInterface');
        $attr = $vertex->getAttribute();
        $this->assertEquals('SessionHandlerInterface', $attr['label']);
    }

    public function testCompactLabelWithNS()
    {
        $vertex = new InterfaceVertex('Full\Qualified\Class\Name\Sample');
        $attr = $vertex->getAttribute();
        $this->assertEquals("FQCN".StaticAnalysis::LABEL_DELIMITER."Sample", $attr['label']);
    }

}
