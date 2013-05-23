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
                                ->addStmt(
                                        $fac
                                        ->method('pur')
                                )
                        )->getNode();

        $this->graph->expects($this->at(0))
                ->method('addVertex')
                ->with($this->vertexConstraint('class', 'Kitty\Soft'));
        $this->graph->expects($this->at(1))
                ->method('addVertex')
                ->with($this->vertexConstraint('method', 'Kitty\Soft::pur'));
        $this->graph->expects($this->at(2))
                ->method('addVertex')
                ->with($this->vertexConstraint('impl', 'Kitty\Soft::pur')
        );
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
                                ->addStmt(
                                        $fac
                                        ->method('pur')
                                )
                        )->getNode();

        $package[1] = $fac->file('sample2.php')
                        ->ns('Kitty')
                        ->declaring(
                                $fac->class('Warm')
                                ->addStmt(
                                        $fac
                                        ->method('pur')
                                )
                        )->getNode();

        $this->graph->expects($this->exactly(5))
                ->method('addVertex');

        $this->graph->expects($this->at(1))
                ->method('addVertex')
                ->with($this->vertexConstraint('impl', 'Kitty\Soft::pur'));

        $this->graph->expects($this->at(4))
                ->method('addVertex')
                ->with($this->vertexConstraint('impl', 'Kitty\Warm::pur'));

        $this->graph->expects($this->exactly(6))
                ->method('addEdge');

        $this->graph->expects($this->at(5))
                ->method('addEdge')
                ->with($this->vertexConstraint('class', 'Kitty\Soft'), $this->vertexConstraint('class', 'Kitty\Warm'));

        $this->graph->expects($this->at(6))
                ->method('addEdge')
                ->with($this->vertexConstraint('impl', 'Kitty\Soft::pur'), $this->vertexConstraint('class', 'Kitty\Soft'));

        $this->graph->expects($this->at(7))
                ->method('addEdge')
                ->with($this->vertexConstraint('class', 'Kitty\Soft'), $this->vertexConstraint('impl', 'Kitty\Soft::pur'));

        $this->director->compile($package);
    }

    protected function vertexConstraint($type, $name)
    {
        return $this->logicalAnd($this
                                ->isInstanceOf('Trismegiste\Mondrian\Transform\Vertex\\' . ucfirst($type) . 'Vertex'), $this
                                ->attributeEqualTo('name', $name));
    }

}