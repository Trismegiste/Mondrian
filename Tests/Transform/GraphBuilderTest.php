<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Transform;

use Trismegiste\Mondrian\Transform\GraphBuilder;
use Trismegiste\Mondrian\Builder\Compiler\Director;
use Trismegiste\Mondrian\Parser\BuilderFactory;

/**
 * GraphBuilderTest tests the builder compiler
 */
class GraphBuilderTest extends \PHPUnit_Framework_TestCase
{

    protected $builder;
    protected $director;
    protected $logger;
    protected $graph;

    protected function setUp()
    {
        $conf = array('calling' => array());
        $this->graph = $this->getMock('Trismegiste\Mondrian\Graph\Graph');
        $this->logger = $this->getMock('Trismegiste\Mondrian\Transform\Logger\LoggerInterface');
        $this->builder = new GraphBuilder($conf, $this->graph, $this->logger);
        $this->director = new Director($this->builder);
    }

    public function testOneBuilding()
    {
        $fac = new BuilderFactory();
        $file = $fac->file('sample.php')
                        ->ns('Kitty')
                        ->declaring(
                                $fac->class('Soft')
                                ->implement('Warm')
                                ->addStmt($fac->method('purr'))
                        )->getNode();

        $this->expectsAddVertex(0, 'class', 'Kitty\Soft');
        $this->expectsAddVertex(1, 'method', 'Kitty\Soft::purr');
        $this->expectsAddVertex(2, 'impl', 'Kitty\Soft::purr');
        $this->graph->expects($this->exactly(3))
                ->method('addEdge');

        $this->director->compile(array($file));
    }

    public function testConcreteInheritance()
    {
        $fac = new BuilderFactory();
        $package[0] = $fac->file('sample1.php')
                        ->ns('Kitty')
                        ->declaring(
                                $fac->class('Soft')
                                ->extend('Warm')
                                ->addStmt($fac->method('purr'))
                        )->getNode();

        $package[1] = $fac->file('sample2.php')
                        ->ns('Kitty')
                        ->declaring(
                                $fac->class('Warm')
                                ->addStmt($fac->method('purr'))
                        )->getNode();

        $this->graph->expects($this->exactly(5))->method('addVertex');
        $this->expectsAddVertex(1, 'impl', 'Kitty\Soft::purr');
        $this->expectsAddVertex(4, 'impl', 'Kitty\Warm::purr');

        $this->graph->expects($this->exactly(6))->method('addEdge');
        $this->expectsAddEdge(5, 'class', 'Kitty\Soft', 'class', 'Kitty\Warm');
        $this->expectsAddEdge(6, 'impl', 'Kitty\Soft::purr', 'class', 'Kitty\Soft');
        $this->expectsAddEdge(7, 'class', 'Kitty\Soft', 'impl', 'Kitty\Soft::purr');

        $this->director->compile($package);
    }

    protected function vertexConstraint($type, $name)
    {
        return $this->logicalAnd($this
                                ->isInstanceOf('Trismegiste\Mondrian\Transform\Vertex\\' . ucfirst($type) . 'Vertex'), $this
                                ->attributeEqualTo('name', $name));
    }

    protected function expectsAddVertex($idx, $type, $name)
    {
        $this->graph->expects($this->at($idx))
                ->method('addVertex')
                ->with($this->vertexConstraint($type, $name));
    }

    protected function expectsAddEdge($idx, $type1, $name1, $type2, $name2)
    {
        $this->graph->expects($this->at($idx))
                ->method('addEdge')
                ->with($this->vertexConstraint($type1, $name1), $this->vertexConstraint($type2, $name2));
    }

}