<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Transform;

use Trismegiste\Mondrian\Transform\Grapher;
use Trismegiste\Mondrian\Graph\Graph;

/**
 * GrapherTest tests for Grapher
 *
 * @author flo
 */
class GrapherTest extends \PHPUnit_Framework_TestCase
{

    protected $grapher;

    protected function setUp()
    {
        $this->grapher = new Grapher();
    }

    public function testOneClass()
    {
        $iter = array(__DIR__ . '/../Fixtures/Graph/OneClass.php');
        $result = $this->grapher->parse($iter);
        $v = $result->getVertexSet();
        $this->assertCount(1, $v);
        $this->assertEquals('Trismegiste\Mondrian\Tests\Fixtures\Graph\OneClass', $v[0]->getName());
        $this->assertCount(0, $result->getEdgeSet());
    }

    public function testInheritance()
    {
        $iter = array(__DIR__ . '/../Fixtures/Graph/Inheritance.php');
        $result = $this->grapher->parse($iter);
        $this->assertCount(4, $result->getVertexSet());
        $this->assertCount(3, $result->getEdgeSet());
    }

    public function testInterfaceInheritance()
    {
        $iter = array(__DIR__ . '/../Fixtures/Graph/Interface.php');
        $result = $this->grapher->parse($iter);
        $this->assertCount(4, $result->getVertexSet());
        $this->assertCount(3, $result->getEdgeSet());
    }

    public function testEmbedMethod()
    {
        $iter = array(__DIR__ . '/../Fixtures/Graph/Concrete.php');
        $result = $this->grapher->parse($iter);
        $this->assertCount(3, $result->getVertexSet());
        $this->assertCount(3, $result->getEdgeSet());
    }

    public function testDecoupleMethod()
    {
        $iter = array(__DIR__ . '/../Fixtures/Graph/NotConcrete.php',
            __DIR__ . '/../Fixtures/Graph/Contract.php');
        $result = $this->grapher->parse($iter);
        $this->assertCount(4, $result->getVertexSet());
        $this->assertCount(4, $result->getEdgeSet());
    }

    public function testDecoupleMethodParam()
    {
        $fqcnClass = 'Trismegiste\Mondrian\Tests\Fixtures\Graph\NotConcreteParam';
        $fqcnInterface = 'Trismegiste\Mondrian\Tests\Fixtures\Graph\ContractParam';
        $iter = array(__DIR__ . '/../Fixtures/Graph/NotConcreteParam.php',
            __DIR__ . '/../Fixtures/Graph/ContractParam.php');
        $result = $this->grapher->parse($iter);
        $this->assertCount(5, $result->getVertexSet());
        $this->assertEdges(array(
            array(
                array('Class', $fqcnClass),
                array('Interface', $fqcnInterface)
            ),
            array(
                array('Class', $fqcnClass),
                array('Impl', "$fqcnClass::setter")
            ),
            array(
                array('Impl', "$fqcnClass::setter"),
                array('Class', $fqcnClass)
            ),
            array(
                array('Interface', $fqcnInterface),
                array('Method', "$fqcnInterface::setter")
            ),
            array(
                array('Method', "$fqcnInterface::setter"),
                array('Param', "$fqcnInterface::setter/0")
            ),
            array(
                array('Impl', "$fqcnClass::setter"),
                array('Param', "$fqcnInterface::setter/0")
            )
                )
                , $result);
    }

    protected function findVertex(Graph $g, $type, $name)
    {
        foreach ($g->getVertexSet() as $vertex) {
            if ((get_class($vertex) == $type) && ($vertex->getName() == $name)) {
                return $vertex;
            }
        }
        return null;
    }

    protected function assertEdges(array $search, Graph $g)
    {
        $nsVertex = 'Trismegiste\Mondrian\Transform\Vertex\\';
        $edge = $g->getEdgeSet();
        $this->assertCount(count($search), $edge);
        foreach ($search as $item) {
            $src = $this->findVertex($g, $nsVertex . $item[0][0] . 'Vertex', $item[0][1]);
            $this->assertNotNull($src, $item[0][0]);
            $dst = $this->findVertex($g, $nsVertex . $item[1][0] . 'Vertex', $item[1][1]);
            $this->assertNotNull($dst, $item[1][0]);
            $e = $g->searchEdge($src, $dst);
            $this->assertNotNull($e, "{$item[0][1]} -> {$item[1][1]}");
        }
    }

    public function testExternalInterfaceInheritance()
    {
        $nsVertex = 'Trismegiste\Mondrian\Transform\Vertex\\';
        $iter = array(__DIR__ . '/../Fixtures/Graph/InheritExtra.php');
        $result = $this->grapher->parse($iter);
        $this->assertCount(2, $result->getVertexSet());
        $this->assertNotNull(
                $this->findVertex(
                        $result, $nsVertex . "ClassVertex", 'Trismegiste\Mondrian\Tests\Fixtures\Graph\InheritExtra'));
        $this->assertNotNull(
                $this->findVertex(
                        $result, $nsVertex . "ImplVertex", 'Trismegiste\Mondrian\Tests\Fixtures\Graph\InheritExtra::getIterator'));
        $this->assertCount(2, $result->getEdgeSet());
    }

    public function testDecoupledMethodWithTypedParam()
    {
        $fqcnClass = 'Trismegiste\Mondrian\Tests\Fixtures\Graph\NotConcreteTypedParam';
        $fqcnInterface = 'Trismegiste\Mondrian\Tests\Fixtures\Graph\ContractTypedParam';
        $fqcnOtherInterface = 'Trismegiste\Mondrian\Tests\Fixtures\Graph\Contract';
        $iter = array(
            __DIR__ . '/../Fixtures/Graph/NotConcreteTypedParam.php',
            __DIR__ . '/../Fixtures/Graph/ContractTypedParam.php',
            __DIR__ . '/../Fixtures/Graph/Contract.php'
        );
        $result = $this->grapher->parse($iter);
        $this->assertCount(7, $result->getVertexSet());
        $this->assertEdges(array(
            array(
                array('Class', $fqcnClass),
                array('Interface', $fqcnInterface)
            ),
            array(
                array('Class', $fqcnClass),
                array('Impl', "$fqcnClass::setter")
            ),
            array(
                array('Impl', "$fqcnClass::setter"),
                array('Class', $fqcnClass)
            ),
            array(
                array('Interface', $fqcnInterface),
                array('Method', "$fqcnInterface::setter")
            ),
            array(
                array('Method', "$fqcnInterface::setter"),
                array('Param', "$fqcnInterface::setter/0")
            ),
            array(
                array('Impl', "$fqcnClass::setter"),
                array('Param', "$fqcnInterface::setter/0")
            ),
            array(
                array('Param', "$fqcnInterface::setter/0"),
                array('Interface', $fqcnOtherInterface)
            ),
            array(
                array('Interface', $fqcnOtherInterface),
                array('Method', "$fqcnOtherInterface::simple")
            )
                )
                , $result);
    }

    public function testOutsideInheritance()
    {
        $iter = array(__DIR__ . '/../Fixtures/Graph/OutsideEdge.php');
        $result = $this->grapher->parse($iter);
        $this->assertCount(4, $result->getVertexSet());
    }

    public function testOutsideSignature()
    {
        $iter = array(__DIR__ . '/../Fixtures/Graph/OutsideSignature.php');
        $result = $this->grapher->parse($iter);
        $this->assertCount(2, $result->getVertexSet());
    }

}