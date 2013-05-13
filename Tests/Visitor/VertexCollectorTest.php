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
    protected $reflection;
    protected $vertex;
    protected $graph;

    protected function setUp()
    {
        $this->reflection = $this->getMockBuilder('Trismegiste\Mondrian\Transform\ReflectionContext')
                ->getMock();
        $this->vertex = $this->getMockBuilder('Trismegiste\Mondrian\Transform\GraphContext')
                ->disableOriginalConstructor()
                ->getMock();
        $this->graph = $this->getMockBuilder('Trismegiste\Mondrian\Graph\Graph')
                ->getMock();
        $this->visitor = new VertexCollector($this->reflection, $this->vertex, $this->graph);
    }

    public function getTypeNodeSetting()
    {
        $vertexNS = 'Trismegiste\Mondrian\Transform\Vertex\\';
        $nsNode = new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name('Tubular'));
        $classNode = new \PHPParser_Node_Stmt_Class('Bells');
        $interfNode = new \PHPParser_Node_Stmt_Interface('Bells');
        return array(
            array('class', 'Tubular\Bells', $vertexNS . 'ClassVertex', array($nsNode, $classNode)),
            array('interface', 'Tubular\Bells', $vertexNS . 'InterfaceVertex', array($nsNode, $interfNode))
        );
    }

    /**
     * @dataProvider getTypeNodeSetting
     */
    public function testNoNewClassVertex($type, $fqcn, $graphVertex, array $nodeList)
    {
        $this->vertex
                ->expects($this->once())
                ->method('existsVertex')
                ->with($type, $fqcn)
                ->will($this->returnValue(true));

        $this->vertex
                ->expects($this->never())
                ->method('addVertex');

        foreach ($nodeList as $node) {
            $this->visitor->enterNode($node);
        }
    }

    /**
     * @dataProvider getTypeNodeSetting
     */
    public function testNewClassVertex($type, $fqcn, $graphVertex, array $nodeList)
    {
        $this->vertex
                ->expects($this->once())
                ->method('existsVertex')
                ->with($type, $fqcn)
                ->will($this->returnValue(false));

        $this->vertex
                ->expects($this->once())
                ->method('indicesVertex')
                ->with($type, $fqcn);

        $this->graph
                ->expects($this->once())
                ->method('addVertex')
                ->with($this->isInstanceOf($graphVertex));

        foreach ($nodeList as $node) {
            $this->visitor->enterNode($node);
        }
    }

    /**
     * @dataProvider getTypeNodeSetting
     */
    public function testNewMethodVertex($type, $fqcn, $graphVertex, array $nodeList)
    {
        $method = new \PHPParser_Node_Stmt_ClassMethod('crisis');
        $method->params[] = new \PHPParser_Node_Param('incantations');
        $nodeList[] = $method;

        $this->reflection
                ->expects($this->once())
                ->method('getDeclaringClass')
                ->with($fqcn, 'crisis')
                ->will($this->returnValue($fqcn));

        $this->reflection
                ->expects($this->once())
                ->method('isInterface')
                ->with($fqcn)
                ->will($this->returnValue($type == 'interface'));

        $this->graph
                ->expects($this->exactly($type == 'interface' ? 3 : 4))
                ->method('addVertex');

        $this->graph
                ->expects($this->at(0))
                ->method('addVertex')
                ->with($this->isInstanceOf($graphVertex));

        $this->graph
                ->expects($this->at(1))
                ->method('addVertex')
                ->with($this->isInstanceOf('Trismegiste\Mondrian\Transform\Vertex\MethodVertex'));

        $this->graph
                ->expects($this->at(2))
                ->method('addVertex')
                ->with($this->isInstanceOf('Trismegiste\Mondrian\Transform\Vertex\ParamVertex'));

        if ($type != 'interface') {
            $this->graph
                    ->expects($this->at(3))
                    ->method('addVertex')
                    ->with($this->isInstanceOf('Trismegiste\Mondrian\Transform\Vertex\ImplVertex'));
        }

        foreach ($nodeList as $node) {
            $this->visitor->enterNode($node);
        }
    }

}
