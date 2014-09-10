<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Transform\Vertex;

/**
 * StaticAnalysisTest is a test for StaticAnalysis vertex superclass
 */
class StaticAnalysisTest extends \PHPUnit_Framework_TestCase
{

    protected $vertex;

    protected function setUp()
    {
        $this->vertex = $this
                ->getMockForAbstractClass('Trismegiste\Mondrian\Transform\Vertex\StaticAnalysis', array('a'));
        $this->vertex->expects($this->any())
                ->method('getSpecific')
                ->will($this->returnValue(array()));
    }

    public function testAttribute()
    {
        $this->assertEquals(array(), $this->vertex->getAttribute());
    }

    public function testCentralityMeta()
    {
        $this->vertex->setMeta('centrality', 5);
        $this->assertArrayHasKey('color', $this->vertex->getAttribute());
    }

}
