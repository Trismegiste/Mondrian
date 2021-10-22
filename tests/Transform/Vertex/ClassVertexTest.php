<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Transform\Vertex;

use Trismegiste\Mondrian\Transform\Vertex\ClassVertex;
use Trismegiste\Mondrian\Transform\Vertex\ImplVertex;
use Trismegiste\Mondrian\Transform\Vertex\StaticAnalysis;

/**
 * ClassVertexTest is an class vertex
 */
class ClassVertexTest extends \PHPUnit\Framework\TestCase
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
        $this->assertEquals("FQCN".StaticAnalysis::LABEL_DELIMITER."Sample", $attr['label']);
    }

}
