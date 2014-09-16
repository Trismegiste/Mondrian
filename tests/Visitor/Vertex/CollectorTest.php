<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Visitor\Vertex;

use Trismegiste\Mondrian\Visitor\Vertex\Collector;

/**
 * CollectorTest is simple tests for Vertex\Collector visitor
 */
class CollectorTest extends \PHPUnit_Framework_TestCase
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
        $this->visitor = new Collector($this->reflection, $this->vertex, $this->graph);
    }

    public function getTypeNodeSetting()
    {
        $vertexNS = 'Trismegiste\Mondrian\Transform\Vertex\\';
        $fileNode = new \Trismegiste\Mondrian\Parser\PhpFile('dummy', []);
        $nsNode = new \PHPParser_Node_Stmt_Namespace(new \PHPParser_Node_Name('Tubular'));
        $classNode = new \PHPParser_Node_Stmt_Class('Bells');
        $interfNode = new \PHPParser_Node_Stmt_Interface('Bells');
        $traitNode = new \PHPParser_Node_Stmt_Trait('Bells');
        return array(
            array('class', 'Tubular\Bells', $vertexNS . 'ClassVertex', array($fileNode, $nsNode, $classNode)),
            array('interface', 'Tubular\Bells', $vertexNS . 'InterfaceVertex', array($fileNode, $nsNode, $interfNode)),
            array('trait', 'Tubular\Bells', $vertexNS . 'TraitVertex', array($fileNode, $nsNode, $traitNode))
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

    public function testNewMethodVertexForClass()
    {
        list($type, $fqcn, $graphVertex, $nodeList) = $this->getTypeNodeSetting()[0];
        $method = new \PHPParser_Node_Stmt_ClassMethod('crisis');
        $method->params[] = new \PHPParser_Node_Param('incantations');
        $nodeList[] = $method;

        $this->reflection
                ->expects($this->once())
                ->method('getDeclaringClass')
                ->with($fqcn, 'crisis')
                ->will($this->returnValue($fqcn));

        $this->graph
                ->expects($this->exactly(4))
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

        $this->graph
                ->expects($this->at(3))
                ->method('addVertex')
                ->with($this->isInstanceOf('Trismegiste\Mondrian\Transform\Vertex\ImplVertex'));

        foreach ($nodeList as $node) {
            $this->visitor->enterNode($node);
        }
    }

    public function testNewMethodVertexForInterface()
    {
        list($type, $fqcn, $graphVertex, $nodeList) = $this->getTypeNodeSetting()[1];
        $method = new \PHPParser_Node_Stmt_ClassMethod('crisis');
        $method->params[] = new \PHPParser_Node_Param('incantations');
        $nodeList[] = $method;

        $this->reflection
                ->expects($this->once())
                ->method('getDeclaringClass')
                ->with($fqcn, 'crisis')
                ->will($this->returnValue($fqcn));

        $this->graph
                ->expects($this->exactly(3))
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

        foreach ($nodeList as $node) {
            $this->visitor->enterNode($node);
        }
    }

    public function testNewImplementationVertexForTrait()
    {
        list($type, $fqcn, $graphVertex, $nodeList) = $this->getTypeNodeSetting()[2];
        $method = new \PHPParser_Node_Stmt_ClassMethod('crisis');
        $method->params[] = new \PHPParser_Node_Param('incantations');
        $nodeList[] = $method;

        $this->reflection
                ->expects($this->once())
                ->method('getClassesUsingTraitForDeclaringMethod')
                ->with($fqcn, 'crisis')
                ->will($this->returnValue([]));

        $this->graph
                ->expects($this->exactly(3))
                ->method('addVertex');

        $this->graph
                ->expects($this->at(0))
                ->method('addVertex')
                ->with($this->isInstanceOf($graphVertex));

        $this->graph
                ->expects($this->at(1))
                ->method('addVertex')
                ->with($this->isInstanceOf('Trismegiste\Mondrian\Transform\Vertex\ImplVertex'));

        $this->graph
                ->expects($this->at(2))
                ->method('addVertex')
                ->with($this->isInstanceOf('Trismegiste\Mondrian\Transform\Vertex\ParamVertex'));

        foreach ($nodeList as $node) {
            $this->visitor->enterNode($node);
        }
    }

    public function testCopyPasteImportedMethodFromTrait()
    {
        list($type, $fqcn, $graphVertex, $nodeList) = $this->getTypeNodeSetting()[2];

        $method = new \PHPParser_Node_Stmt_ClassMethod('crisis');
        $method->params[] = new \PHPParser_Node_Param('incantations');
        $nodeList[] = $method;

        $this->reflection
                ->expects($this->once())
                ->method('getClassesUsingTraitForDeclaringMethod')
                ->with($fqcn, 'crisis')
                ->will($this->returnValue(['TraitUser1', 'TraitUser2']));

        $this->graph
                ->expects($this->exactly(5))
                ->method('addVertex');

        // the trait vertex
        $this->graph
                ->expects($this->at(0))
                ->method('addVertex')
                ->with($this->isInstanceOf($graphVertex));

        // implementation
        $this->graph
                ->expects($this->at(1))
                ->method('addVertex')
                ->with($this->isInstanceOf('Trismegiste\Mondrian\Transform\Vertex\ImplVertex'));
        $this->graph
                ->expects($this->at(2))
                ->method('addVertex')
                ->with($this->isInstanceOf('Trismegiste\Mondrian\Transform\Vertex\ParamVertex'));

        // first copy-pasted method
        $this->graph
                ->expects($this->at(3))
                ->method('addVertex')
                ->with($this->isInstanceOf('Trismegiste\Mondrian\Transform\Vertex\MethodVertex'));

        // second copy-pasted method
        $this->graph
                ->expects($this->at(4))
                ->method('addVertex')
                ->with($this->isInstanceOf('Trismegiste\Mondrian\Transform\Vertex\MethodVertex'));

        foreach ($nodeList as $node) {
            $this->visitor->enterNode($node);
        }
    }

}
