<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Visitor;

use Trismegiste\Mondrian\Visitor\VertexCollector;

/**
 * VertexCollectorTest is simple tests for VertexCollector visitor
 */
class VertexCollectorTest extends \PHPUnit_Framework_TestCase
{

    protected $visitor;
    protected $context;
    protected $graph;

    protected function setUp()
    {
        $this->context = $this->getMockBuilder('Trismegiste\Mondrian\Transform\Context')
                ->getMock();
        $this->graph = $this->getMockBuilder('Trismegiste\Mondrian\Graph\Graph')
                ->getMock();
        $this->visitor = new VertexCollector($this->context, $this->graph);
    }

    public function testNoNewClassVertex()
    {
        $this->context
                ->expects($this->once())
                ->method('existsVertex')
                ->with('class', 'Tubular\Bells')
                ->will($this->returnValue(true));

        $this->graph
                ->expects($this->never())
                ->method('addVertex');

        $this->visitor->enterNode(new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name('Tubular')));
        $this->visitor->enterNode(new \PHPParser_Node_Stmt_Class('Bells'));
    }

    public function testNewClassVertex()
    {
        $this->context
                ->expects($this->once())
                ->method('existsVertex')
                ->with('class', 'Tubular\Bells')
                ->will($this->returnValue(false));

        $this->graph
                ->expects($this->once())
                ->method('addVertex')
                ->with($this->isInstanceOf('Trismegiste\Mondrian\Transform\Vertex\ClassVertex'));

        $this->visitor->enterNode(new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name('Tubular')));
        $this->visitor->enterNode(new \PHPParser_Node_Stmt_Class('Bells'));
    }

}