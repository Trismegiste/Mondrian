<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Graph;

use Trismegiste\Mondrian\Graph\Vertex;
use Trismegiste\Mondrian\Graph\PrettyPrint;
use Trismegiste\Mondrian\Graph\Digraph;

/**
 * PrettyPrintTest is a test for the PrettyPrint decorator
 */
class PrettyPrintTest extends \PHPUnit_Framework_TestCase
{

    protected $graph;

    protected function setUp()
    {
        $this->graph = new PrettyPrint(new Digraph());
    }

    protected function tearDown()
    {
        unset($this->graph);
    }

    public function testEmpty()
    {
        $str = (string) $this->graph;
        $this->assertEquals('', $str);
    }

    public function testSimple()
    {
        $this->graph->addEdge(new Vertex('a'), new Vertex('b'));
        $str = (string) $this->graph;
        $this->assertStringStartsWith('Vertex : a', $str);
    }

}
