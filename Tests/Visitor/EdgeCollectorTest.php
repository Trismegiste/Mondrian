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
    protected $vertex;

    protected function setUp()
    {
        $this->context = $this->getMockBuilder('Trismegiste\Mondrian\Transform\Context')
                ->getMock();
        $this->graph = $this->getMockBuilder('Trismegiste\Mondrian\Graph\Graph')
                ->getMock();
        $this->visitor = new EdgeCollector($this->context, $this->graph);

        $vertexNS = 'Trismegiste\Mondrian\Transform\Vertex';
        $this->vertex = array(
            'C' => $this->getMockBuilder("$vertexNS\ClassVertex")
                    ->disableOriginalConstructor()
                    ->getMock(),
            'I' => $this->getMockBuilder("$vertexNS\InterfaceVertex")
                    ->disableOriginalConstructor()
                    ->getMock(),
            'M' => $this->getMockBuilder("$vertexNS\MethodVertex")
                    ->disableOriginalConstructor()
                    ->getMock(),
            'S' => $this->getMockBuilder("$vertexNS\ImplVertex")
                    ->disableOriginalConstructor()
                    ->getMock()
        );

        $this->context
                ->expects($this->any())
                ->method('findVertex')
                ->will($this->returnValueMap(array(
                            array('class', 'Atavachron\Funnels', $this->vertex['C']),
                            array('class', 'Atavachron\Looking', $this->vertex['C']),
                            array('interface', 'Atavachron\Glass', $this->vertex['I']),
                            array('interface', 'Atavachron\Berwell', $this->vertex['I']),
                            array('method', "Atavachron\Funnels::sand", $this->vertex['M']),
                            array('impl', "Atavachron\Funnels::sand", $this->vertex['S'])
        )));
    }

    /**
     * Test for :
     *  * C -> C
     *  * C -> I
     */
    public function testClassInheritance()
    {
        $nsNode = new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name('Atavachron'));
        $classNode = new \PHPParser_Node_Stmt_Class('Funnels');
        $classNode->extends = new \PHPParser_Node_Name('Looking');
        $classNode->implements[] = new \PHPParser_Node_Name('Glass');

        $this->graph
                ->expects($this->at(0))
                ->method('addEdge')
                ->with($this->vertex['C'], $this->vertex['C']);

        $this->graph
                ->expects($this->at(1))
                ->method('addEdge')
                ->with($this->vertex['C'], $this->vertex['I']);

        $this->visitor->enterNode($nsNode);
        $this->visitor->enterNode($classNode);
    }

    /**
     * Test for :
     *  * I -> I
     */
    public function testInterfaceInheritance()
    {
        $node[0] = new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name('Atavachron'));
        $node[1] = new \PHPParser_Node_Stmt_Interface('Berwell');
        $node[1]->extends[] = new \PHPParser_Node_Name('Glass');

        $this->graph
                ->expects($this->once())
                ->method('addEdge')
                ->with($this->vertex['I'], $this->vertex['I']);

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
        $nodeList[0] = new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name('Atavachron'));
        $nodeList[1] = new \PHPParser_Node_Stmt_Class('Funnels');
        $nodeList[2] = new \PHPParser_Node_Stmt_ClassMethod('sand');

        $this->context
                ->expects($this->once())
                ->method('getDeclaringClass')
                ->with('Atavachron\Funnels', 'sand')
                ->will($this->returnValue('Atavachron\Funnels'));

        $this->graph
                ->expects($this->at(0))
                ->method('addEdge')
                ->with($this->vertex['C'], $this->vertex['M']);

        $this->graph
                ->expects($this->at(2))
                ->method('addEdge')
                ->with($this->vertex['M'], $this->vertex['S']);

        $this->graph
                ->expects($this->at(1))
                ->method('addEdge')
                ->with($this->vertex['S'], $this->vertex['C']);

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
        $nodeList[0] = new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name('Atavachron'));
        $nodeList[1] = new \PHPParser_Node_Stmt_Class('Funnels');
        $nodeList[2] = new \PHPParser_Node_Stmt_ClassMethod('sand');

        $this->graph
                ->expects($this->at(1))
                ->method('addEdge')
                ->with($this->vertex['C'], $this->vertex['S']);

        $this->graph
                ->expects($this->at(0))
                ->method('addEdge')
                ->with($this->vertex['S'], $this->vertex['C']);

        foreach ($nodeList as $node) {
            $this->visitor->enterNode($node);
        }
    }

}