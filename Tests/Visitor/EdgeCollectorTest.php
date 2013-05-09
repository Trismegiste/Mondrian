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
    protected $reflection;
    protected $graph;
    protected $vertex;
    protected $nodeList;
    protected $dictionary;

    protected function setUp()
    {
        $this->reflection = $this->getMockBuilder('Trismegiste\Mondrian\Transform\ReflectionContext')
                ->getMock();
        $this->dictionary = $this->getMockBuilder('Trismegiste\Mondrian\Transform\GraphContext')
                ->disableOriginalConstructor()
                ->getMock();
        $this->graph = $this->getMockBuilder('Trismegiste\Mondrian\Graph\Graph')
                ->getMock();
        $this->visitor = new EdgeCollector($this->reflection, $this->dictionary, $this->graph);

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
                    ->getMock(),
            'P' => $this->getMockBuilder("$vertexNS\ImplVertex")
                    ->disableOriginalConstructor()
                    ->getMock()
        );

        $this->dictionary
                ->expects($this->any())
                ->method('findVertex')
                ->will($this->returnValueMap(array(
                            array('class', 'Atavachron\Funnels', $this->vertex['C']),
                            array('class', 'Atavachron\Looking', $this->vertex['C']),
                            array('interface', 'Atavachron\Glass', $this->vertex['I']),
                            array('interface', 'Atavachron\Berwell', $this->vertex['I']),
                            array('method', 'Atavachron\Berwell::clown', $this->vertex['M']),
                            array('method', "Atavachron\Funnels::sand", $this->vertex['M']),
                            array('impl', "Atavachron\Funnels::sand", $this->vertex['S']),
                            array('param', 'Atavachron\Berwell::clown/0', $this->vertex['P']),
                            array('param', 'Atavachron\Funnels::sand/0', $this->vertex['P']),
        )));

        $this->reflection
                ->expects($this->any())
                ->method('isInterface')
                ->will($this->returnValueMap(array(
                            array('Atavachron\Glass', true),
                            array('Atavachron\Berwell', true)
        )));


        $this->nodeList[0] = new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name('Atavachron'));
    }

    protected function visitNodeList()
    {
        foreach ($this->nodeList as $node) {
            $this->visitor->enterNode($node);
        }
    }

    /**
     * Test for :
     *  * C -> C
     *  * C -> I
     */
    public function testClassInheritance()
    {
        $this->nodeList[1] = new \PHPParser_Node_Stmt_Class('Funnels');
        $this->nodeList[1]->extends = new \PHPParser_Node_Name('Looking');
        $this->nodeList[1]->implements[] = new \PHPParser_Node_Name('Glass');

        $this->graph
                ->expects($this->at(0))
                ->method('addEdge')
                ->with($this->vertex['C'], $this->vertex['C']);

        $this->graph
                ->expects($this->at(1))
                ->method('addEdge')
                ->with($this->vertex['C'], $this->vertex['I']);

        $this->visitNodeList();
    }

    /**
     * Test for :
     *  * I -> I
     */
    public function testInterfaceInheritance()
    {
        $this->nodeList[1] = new \PHPParser_Node_Stmt_Interface('Berwell');
        $this->nodeList[1]->extends[] = new \PHPParser_Node_Name('Glass');

        $this->graph
                ->expects($this->once())
                ->method('addEdge')
                ->with($this->vertex['I'], $this->vertex['I']);

        $this->visitNodeList();
    }

    /**
     * Test for :
     *  * C -> M
     *  * M -> S
     *  * S -> C
     */
    public function testConcreteMethod()
    {
        $this->nodeList[1] = new \PHPParser_Node_Stmt_Class('Funnels');
        $this->nodeList[2] = new \PHPParser_Node_Stmt_ClassMethod('sand');

        $this->reflection
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

        $this->visitNodeList();
    }

    /**
     * Test for :
     *  * C -> S
     *  * S -> C
     */
    public function testOverridenMethod()
    {
        $this->nodeList[1] = new \PHPParser_Node_Stmt_Class('Funnels');
        $this->nodeList[2] = new \PHPParser_Node_Stmt_ClassMethod('sand');

        $this->graph
                ->expects($this->at(1))
                ->method('addEdge')
                ->with($this->vertex['C'], $this->vertex['S']);

        $this->graph
                ->expects($this->at(0))
                ->method('addEdge')
                ->with($this->vertex['S'], $this->vertex['C']);

        $this->visitNodeList();
    }

    /**
     * Test for :
     *  * I -> M
     */
    public function testInterfaceMethod()
    {
        $this->nodeList[1] = new \PHPParser_Node_Stmt_Interface('Berwell');
        $this->nodeList[2] = new \PHPParser_Node_Stmt_ClassMethod('clown');

        $this->reflection
                ->expects($this->once())
                ->method('getDeclaringClass')
                ->with('Atavachron\Berwell', 'clown')
                ->will($this->returnValue('Atavachron\Berwell'));

        $this->graph
                ->expects($this->once())
                ->method('addEdge')
                ->with($this->vertex['I'], $this->vertex['M']);

        $this->visitNodeList();
    }

    /**
     * Test for :
     *  * M -> P
     *  * P -> C
     */
    public function testTypedParameterInInterface()
    {
        $this->nodeList[1] = new \PHPParser_Node_Stmt_Interface('Berwell');
        $this->nodeList[2] = new \PHPParser_Node_Stmt_ClassMethod('clown');
        $this->nodeList[2]->params[] = new \PHPParser_Node_Param('obj', null, new \PHPParser_Node_Name('Funnels'));

        $this->reflection
                ->expects($this->once())
                ->method('getDeclaringClass')
                ->with('Atavachron\Berwell', 'clown')
                ->will($this->returnValue('Atavachron\Berwell'));

        $this->graph
                ->expects($this->at(0))
                ->method('addEdge')
                ->with($this->vertex['I'], $this->vertex['M']);

        $this->graph
                ->expects($this->at(1))
                ->method('addEdge')
                ->with($this->vertex['M'], $this->vertex['P']);

        $this->graph
                ->expects($this->at(2))
                ->method('addEdge')
                ->with($this->vertex['P'], $this->vertex['C']);

        $this->visitNodeList();
    }

    /**
     * Test for :
     *  * S -> P
     */
    public function testNonTypedParameterInClass()
    {
        $this->nodeList[1] = new \PHPParser_Node_Stmt_Class('Funnels');
        $this->nodeList[2] = new \PHPParser_Node_Stmt_ClassMethod('sand');
        $this->nodeList[2]->params[] = new \PHPParser_Node_Param('obj');

        // Method is owned by the class
        $this->reflection
                ->expects($this->once())
                ->method('getDeclaringClass')
                ->with('Atavachron\Funnels', 'sand')
                ->will($this->returnValue('Atavachron\Funnels'));

        // edges :
        $this->graph
                ->expects($this->at(0))
                ->method('addEdge')
                ->with($this->vertex['C'], $this->vertex['M']);

        $this->graph
                ->expects($this->at(1))
                ->method('addEdge')
                ->with($this->vertex['M'], $this->vertex['P']);

        $this->graph
                ->expects($this->at(2))
                ->method('addEdge')
                ->with($this->vertex['S'], $this->vertex['C']);

        $this->graph
                ->expects($this->at(3))
                ->method('addEdge')
                ->with($this->vertex['M'], $this->vertex['S']);

        $this->graph
                ->expects($this->at(4))
                ->method('addEdge')
                ->with($this->vertex['S'], $this->vertex['P']);

        $this->visitNodeList();
    }

    /**
     * Test for :
     *  * S -> C
     */
    public function testNewInstance()
    {
        $this->nodeList[1] = new \PHPParser_Node_Stmt_Class('Funnels');
        $this->nodeList[2] = new \PHPParser_Node_Stmt_ClassMethod('sand');
        $this->nodeList[3] = new \PHPParser_Node_Expr_New(new \PHPParser_Node_Name('Looking'));

        // edges :
        $this->graph
                ->expects($this->at(2))
                ->method('addEdge')
                ->with($this->vertex['S'], $this->vertex['C']);

        $this->visitNodeList();
    }

    /**
     * Test for :
     *  * S -> M
     */
    public function testSimpleCallFallback()
    {
        $this->nodeList[1] = new \PHPParser_Node_Stmt_Class('Funnels');
        $this->nodeList[2] = new \PHPParser_Node_Stmt_ClassMethod('sand');
        $this->nodeList[3] = new \PHPParser_Node_Expr_MethodCall(
                new \PHPParser_Node_Expr_Variable('obj'), 'clown');

        $this->dictionary
                ->expects($this->once())
                ->method('findAllMethodSameName')
                ->with('clown')
                ->will($this->returnValue(array($this->vertex['M'])));

        $this->dictionary
                ->expects($this->any())
                ->method('getExcludedCall')
                ->will($this->returnValue(array()));

        // edges :
        $this->graph
                ->expects($this->at(2))
                ->method('addEdge')
                ->with($this->vertex['S'], $this->vertex['M']);

        $this->visitNodeList();
    }

    /**
     * Test for :
     *  * S -> M
     */
    public function testTypedCall()
    {
        $this->nodeList[1] = new \PHPParser_Node_Stmt_Class('Funnels');
        $this->nodeList[2] = new \PHPParser_Node_Stmt_ClassMethod('sand');
        $this->nodeList[2]->params[] = new \PHPParser_Node_Param('obj', null, new \PHPParser_Node_Name('Berwell'));
        $this->nodeList[3] = new \PHPParser_Node_Expr_MethodCall(new \PHPParser_Node_Expr_Variable('obj'), 'clown');

        $this->dictionary
                ->expects($this->any())
                ->method('getExcludedCall')
                ->will($this->returnValue(array()));

        $this->reflection
                ->expects($this->once())
                ->method('hasDeclaringClass')
                ->will($this->returnValue(true));

        $this->reflection
                ->expects($this->once())
                ->method('findMethodInInheritanceTree')
                ->will($this->returnArgument(0));

        // edges :
        $this->graph
                ->expects($this->at(2))
                ->method('addEdge')
                ->with($this->vertex['S'], $this->vertex['M']);

        $this->visitNodeList();
    }

    /**
     * Test for :
     *  * S -> M
     */
    public function testExcludingCall()
    {
        $this->nodeList[1] = new \PHPParser_Node_Stmt_Class('Funnels');
        $this->nodeList[2] = new \PHPParser_Node_Stmt_ClassMethod('sand');
        $this->nodeList[3] = new \PHPParser_Node_Expr_MethodCall(
                new \PHPParser_Node_Expr_Variable('obj'), 'clown');

        $this->dictionary
                ->expects($this->once())
                ->method('findAllMethodSameName')
                ->with('clown')
                ->will($this->returnValue(array($this->vertex['M'])));

        $this->vertex['M']
                ->expects($this->any())
                ->method('getName')
                ->will($this->returnValue('excluded'));

        $this->dictionary
                ->expects($this->once())
                ->method('getExcludedCall')
                ->with('Atavachron\Funnels', 'sand')
                ->will($this->returnValue(array('excluded')));

        // edges :
        $this->graph
                ->expects($this->exactly(2))
                ->method('addEdge');

        $this->graph
                ->expects($this->at(0))
                ->method('addEdge')
                ->with($this->vertex['S'], $this->vertex['C']);

        $this->graph
                ->expects($this->at(1))
                ->method('addEdge')
                ->with($this->vertex['C'], $this->vertex['S']);

        $this->visitNodeList();
    }

}