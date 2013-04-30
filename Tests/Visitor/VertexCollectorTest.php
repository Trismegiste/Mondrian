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
        $this->context
                ->expects($this->once())
                ->method('existsVertex')
                ->with($type, $fqcn)
                ->will($this->returnValue(true));

        $this->graph
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
        $this->context
                ->expects($this->once())
                ->method('existsVertex')
                ->with($type, $fqcn)
                ->will($this->returnValue(false));

        $this->context
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

}