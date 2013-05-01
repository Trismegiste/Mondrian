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

    protected function getVertex()
    {
        return $this->getMockBuilder('Trismegiste\Mondrian\Graph\Vertex')
                        ->disableOriginalConstructor()
                        ->getMock();
    }

    /**
     * Test for :
     *  * C -> C
     *  * C -> I
     */
    public function testClassInheritance()
    {
        $vertex = $this->getVertex();
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
        $vertex = $this->getVertex();
        $node[0] = new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name('Atavachron'));
        $node[1] = new \PHPParser_Node_Stmt_Interface('Funnels');
        $node[1]->extends[] = new \PHPParser_Node_Name('Looking');

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
                ->expects($this->once())
                ->method('addEdge')
                ->with($vertex, $vertex);

        foreach ($node as $stmt) {
            $this->visitor->enterNode($stmt);
        }
    }

    /**
     * Test for :
     *  * C -> M
     *  * M -> S
     *  * S -> C
     */
    public function testConcreteMethod()
    {
        $vertex1 = $this->getMockBuilder('Trismegiste\Mondrian\Transform\Vertex\ClassVertex')
                ->disableOriginalConstructor()
                ->getMock();
        $vertex2 = $this->getMockBuilder('Trismegiste\Mondrian\Transform\Vertex\MethodVertex')
                ->disableOriginalConstructor()
                ->getMock();
        $vertex3 = $this->getMockBuilder('Trismegiste\Mondrian\Transform\Vertex\ImplVertex')
                ->disableOriginalConstructor()
                ->getMock();

        $nodeList[0] = new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name('Atavachron'));
        $nodeList[1] = new \PHPParser_Node_Stmt_Class('Funnels');
        $nodeList[2] = new \PHPParser_Node_Stmt_ClassMethod('sand');
        $fqcn = 'Atavachron\Funnels';

        $this->context
                ->expects($this->any())
                ->method('findVertex')
                ->will($this->returnValueMap(array(
                            array('class', $fqcn, $vertex1),
                            array('method', "$fqcn::sand", $vertex2),
                            array('impl', "$fqcn::sand", $vertex3)
        )));

        $this->context
                ->expects($this->once())
                ->method('getDeclaringClass')
                ->with($fqcn, 'sand')
                ->will($this->returnValue($fqcn));

        $this->graph
                ->expects($this->at(0))
                ->method('addEdge')
                ->with($vertex1, $vertex2);

        $this->graph
                ->expects($this->at(2))
                ->method('addEdge')
                ->with($vertex2, $vertex3);

        $this->graph
                ->expects($this->at(1))
                ->method('addEdge')
                ->with($vertex3, $vertex1);

        foreach ($nodeList as $node) {
            $this->visitor->enterNode($node);
        }
    }

    /**
     * Test for :
     *  * C -> S
     *  * S -> C
     */
    public function testOverridenMethod()
    {
        $vertex1 = $this->getMockBuilder('Trismegiste\Mondrian\Transform\Vertex\ClassVertex')
                ->disableOriginalConstructor()
                ->getMock();
        $vertex3 = $this->getMockBuilder('Trismegiste\Mondrian\Transform\Vertex\ImplVertex')
                ->disableOriginalConstructor()
                ->getMock();

        $nodeList[0] = new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name('Atavachron'));
        $nodeList[1] = new \PHPParser_Node_Stmt_Class('Funnels');
        $nodeList[2] = new \PHPParser_Node_Stmt_ClassMethod('sand');
        $fqcn = 'Atavachron\Funnels';

        $this->context
                ->expects($this->any())
                ->method('findVertex')
                ->will($this->returnValueMap(array(
                            array('class', $fqcn, $vertex1),
                            array('impl', "$fqcn::sand", $vertex3)
        )));

        $this->graph
                ->expects($this->at(1))
                ->method('addEdge')
                ->with($vertex1, $vertex3);

        $this->graph
                ->expects($this->at(0))
                ->method('addEdge')
                ->with($vertex3, $vertex1);

        foreach ($nodeList as $node) {
            $this->visitor->enterNode($node);
        }
    }

}