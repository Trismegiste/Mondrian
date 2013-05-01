<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Visitor;

use Trismegiste\Mondrian\Visitor\EdgeCollector;

/**
 * EdgeCollectorTest is simple tests for EdgeCollector visitor. Tests the 
 * grammar implementation of digraph.
 * 
 * Vocabulary :
 *  * C : Class
 *  * I : Interface
 *  * M : Method signature
 *  * P : Param
 *  * S : Method Implementation
 * 
 */
class EdgeCollectorTest extends \PHPUnit_Framework_TestCase
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
        $this->visitor = new EdgeCollector($this->context, $this->graph);
    }

    /**
     * Test for :
     *  * C -> C
     *  * C -> I
     */
    public function testClassInheritance()
    {
        $vertex = $this->getMockBuilder('Trismegiste\Mondrian\Graph\Vertex')
                ->disableOriginalConstructor()
                ->getMock();
        $nsNode = new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name('Atavachron'));
        $classNode = new \PHPParser_Node_Stmt_Class('Funnels');
        $classNode->extends = new \PHPParser_Node_Name('Looking');
        $classNode->implements[] = new \PHPParser_Node_Name('Glass');

        $this->context
                ->expects($this->exactly(3))
                ->method('findVertex')
                ->will($this->returnValue($vertex));

        $this->context
                ->expects($this->at(0))
                ->method('findVertex')
                ->with('class', 'Atavachron\Funnels');

        $this->context
                ->expects($this->at(1))
                ->method('findVertex')
                ->with('class', 'Atavachron\Looking');

        $this->context
                ->expects($this->at(2))
                ->method('findVertex')
                ->with('interface', 'Atavachron\Glass');

        $this->graph
                ->expects($this->exactly(2))
                ->method('addEdge')
                ->with($vertex, $vertex);

        $this->visitor->enterNode($nsNode);
        $this->visitor->enterNode($classNode);
    }

    /**
     * Test for :
     *  * I -> I
     */
    public function testInterfaceInheritance()
    {
        $vertex = $this->getMockBuilder('Trismegiste\Mondrian\Graph\Vertex')
                ->disableOriginalConstructor()
                ->getMock();
        $nsNode = new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name('Atavachron'));
        $classNode = new \PHPParser_Node_Stmt_Interface('Funnels');
        $classNode->extends[] = new \PHPParser_Node_Name('Looking');

        $this->context
                ->expects($this->exactly(2))
                ->method('findVertex')
                ->will($this->returnValue($vertex));

        $this->context
                ->expects($this->at(0))
                ->method('findVertex')
                ->with('interface', 'Atavachron\Funnels');

        $this->context
                ->expects($this->at(1))
                ->method('findVertex')
                ->with('interface', 'Atavachron\Looking');

        $this->graph
                ->expects($this->exactly(1))
                ->method('addEdge')
                ->with($vertex, $vertex);

        $this->visitor->enterNode($nsNode);
        $this->visitor->enterNode($classNode);
    }

}