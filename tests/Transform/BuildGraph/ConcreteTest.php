<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Transform\BuildGraph;

/**
 * ConcreteTest tests for building a graph on concrete class
 */
class ConcreteTest extends GraphBuilderTestCase
{

    public function testOneBuilding()
    {
        $fac = $this->createFactory();
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

        $this->compile(array($file));
    }

    public function testConcreteInheritance()
    {
        $fac = $this->createFactory();
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
        $this->expectsAddEdge(6, 'class', 'Kitty\Soft', 'impl', 'Kitty\Soft::purr');
        $this->expectsAddEdge(7, 'impl', 'Kitty\Soft::purr', 'class', 'Kitty\Soft');

        $this->compile($package);
    }

}