<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Builder\Compiler;

use Trismegiste\Mondrian\Builder\Compiler\Director;

/**
 * DirectorTest tests the director that builds the Compiler with the help of the builder
 */
class DirectorTest extends \PHPUnit\Framework\TestCase
{

    protected $director;
    protected $builder;

    protected function setUp():void
    {
        $this->builder = $this->getMock('Trismegiste\Mondrian\Builder\Compiler\BuilderInterface');
        $this->director = new Director($this->builder);
    }

    public function testBuilding()
    {
        $this->builder
                ->expects($this->once())
                ->method('buildContext');
        $this->builder
                ->expects($this->once())
                ->method('buildCollectors')
                ->will($this->returnValue(array($this->getMock('Trismegiste\Mondrian\Visitor\FqcnHelper'))));
        $this->builder
                ->expects($this->once())
                ->method('buildTraverser')
                ->will($this->returnValue($this->getMock('PHPParser_NodeTraverser')));

        $this->director->compile(array());
    }

}