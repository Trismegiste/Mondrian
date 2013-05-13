<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Transform\Vertex;

use Trismegiste\Mondrian\Transform\Vertex\ClassVertex;

/**
 * ClassVertexTest is an class vertex
 */
class ClassVertexTest extends \PHPUnit_Framework_TestCase
{

    public function testCompactLabelWithoutNS()
    {
        $vertex = new ClassVertex('SessionHandler');
        $attr = $vertex->getAttribute();
        $this->assertEquals('SessionHandler', $attr['label']);
    }

    public function testCompactLabelWithNS()
    {
        $vertex = new ClassVertex('Full\Qualified\Class\Name\Sample');
        $attr = $vertex->getAttribute();
        $this->assertEquals("FQCN\nSample", $attr['label']);
    }

}
