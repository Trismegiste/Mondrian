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
        $iter = array(__DIR__ . '/../Fixtures/Project/OneClass.php');
        $result = $this->grapher->parse($iter);
        $v = $result->getVertexSet();
        $this->assertCount(1, $v);
        $this->assertEquals('Project\OneClass', $v[0]->getName());
        $this->assertCount(0, $result->getEdgeSet());
    }

    public function testInheritance()
    {
        $iter = array(__DIR__ . '/../Fixtures/Project/Inheritance.php');
        $result = $this->grapher->parse($iter);
        $this->assertCount(4, $result->getVertexSet());
        $this->assertCount(3, $result->getEdgeSet());
    }

    public function testInterfaceInheritance()
    {
        $iter = array(__DIR__ . '/../Fixtures/Project/Interface.php');
        $result = $this->grapher->parse($iter);
        $this->assertCount(4, $result->getVertexSet());
        $this->assertCount(3, $result->getEdgeSet());
    }

    public function testEmbedMethod()
    {
        $iter = array(__DIR__ . '/../Fixtures/Project/Concrete.php');
        $result = $this->grapher->parse($iter);
        $this->assertCount(3, $result->getVertexSet());
        $this->assertCount(3, $result->getEdgeSet());
    }

    public function testDecoupleMethod()
    {
        $iter = array(__DIR__ . '/../Fixtures/Project/NotConcrete.php',
            __DIR__ . '/../Fixtures/Project/Contract.php');
        $result = $this->grapher->parse($iter);
        $this->assertCount(4, $result->getVertexSet());
        $this->assertCount(4, $result->getEdgeSet());
    }

    public function testDecoupleMethodParam()
    {
        $fqcnClass = 'Project\NotConcreteParam';
        $fqcnInterface = 'Project\ContractParam';
        $iter = array(__DIR__ . '/../Fixtures/Project/NotConcreteParam.php',
            __DIR__ . '/../Fixtures/Project/ContractParam.php');
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
        $iter = array(__DIR__ . '/../Fixtures/Project/InheritExtra.php');
        $result = $this->grapher->parse($iter);
        $this->assertCount(2, $result->getVertexSet());
        $this->assertNotNull(
                $this->findVertex(
                        $result, $nsVertex . "ClassVertex", 'Project\InheritExtra'));
        $this->assertNotNull(
                $this->findVertex(
                        $result, $nsVertex . "ImplVertex", 'Project\InheritExtra::getIterator'));
        $this->assertCount(2, $result->getEdgeSet());
    }

    public function testDecoupledMethodWithTypedParam()
    {
        $fqcnClass = 'Project\NotConcreteTypedParam';
        $fqcnInterface = 'Project\ContractTypedParam';
        $fqcnOtherInterface = 'Project\Contract';
        $iter = array(
            __DIR__ . '/../Fixtures/Project/NotConcreteTypedParam.php',
            __DIR__ . '/../Fixtures/Project/ContractTypedParam.php',
            __DIR__ . '/../Fixtures/Project/Contract.php'
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
        $iter = array(__DIR__ . '/../Fixtures/Project/OutsideEdge.php');
        $result = $this->grapher->parse($iter);
        $this->assertCount(4, $result->getVertexSet());
    }

    public function testOutsideSignature()
    {
        $iter = array(__DIR__ . '/../Fixtures/Project/OutsideSignature.php');
        $result = $this->grapher->parse($iter);
        $this->assertCount(2, $result->getVertexSet());
    }

    public function testCalling()
    {
        $nsVertex = 'Trismegiste\Mondrian\Transform\Vertex\\';
        $iter = array(__DIR__ . '/../Fixtures/Project/Calling.php',
            __DIR__ . '/../Fixtures/Project/Concrete.php');
        $result = $this->grapher->parse($iter);
        $this->assertCount(8, $result->getVertexSet());
        $this->assertCount(10, $result->getEdgeSet());
        $impl = $this->findVertex($result, $nsVertex . 'ImplVertex', 'Project\Calling::simpleCall');
        $this->assertNotNull($impl);
        $calledMethod = $this->findVertex($result, $nsVertex . 'MethodVertex', 'Project\Concrete::simple');
        $this->assertNotNull($calledMethod);
        $link = $result->searchEdge($impl, $calledMethod);
        $this->assertNotNull($link);
    }

    public function testNewInstance()
    {
        $nsVertex = 'Trismegiste\Mondrian\Transform\Vertex\\';
        $iter = array(__DIR__ . '/../Fixtures/Project/NewInstance.php',
            __DIR__ . '/../Fixtures/Project/Concrete.php');
        $result = $this->grapher->parse($iter);
        $this->assertCount(8, $result->getVertexSet());
        $this->assertCount(10, $result->getEdgeSet());
        $impl = $this->findVertex($result, $nsVertex . 'ImplVertex', 'Project\NewInstance::simpleNew');
        $this->assertNotNull($impl);
        $classVertex = $this->findVertex($result, $nsVertex . 'ClassVertex', 'Project\Concrete');
        $this->assertNotNull($classVertex);
        $link = $result->searchEdge($impl, $classVertex);
        $this->assertNotNull($link);
    }

    public function testFilteringObviousMethodCall()
    {
        $nsVertex = 'Trismegiste\Mondrian\Transform\Vertex\\';
        $iter = array(__DIR__ . '/../Fixtures/Project/FilterCalling.php');
        $result = $this->grapher->parse($iter);

        $this->assertCount(13, $result->getVertexSet());
        $this->assertCount(17, $result->getEdgeSet());
        $impl = $this->findVertex($result, $nsVertex . 'ImplVertex', 'Project\FilterCalling::decorate');
        $this->assertNotNull($impl);
        $succ = $result->getSuccessor($impl);
        $this->assertCount(3, $succ); // the class, the param and one call (not two)
    }

    public function testNoFilteringMethodCallOnOuterClass()
    {
        $nsVertex = 'Trismegiste\Mondrian\Transform\Vertex\\';
        $iter = array(__DIR__ . '/../Fixtures/Project/FilterOuterCalling.php');
        $result = $this->grapher->parse($iter);

        $this->assertCount(10, $result->getVertexSet());
        $this->assertCount(13, $result->getEdgeSet());
        $impl = $this->findVertex($result, $nsVertex . 'ImplVertex', 'Project\FilterCalling::decorate');
        $this->assertNotNull($impl);
        $succ = $result->getSuccessor($impl);
        $this->assertCount(2, $succ); // the class, the param and no call
        // (there is no signature to call since it's an outer class)
    }

}