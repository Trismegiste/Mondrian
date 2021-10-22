<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Builder\Compiler;

use Trismegiste\Mondrian\Builder\Compiler\Director;
use Trismegiste\Mondrian\Parser\PhpFile;

/**
 * AbstractTraverserTest tests the building a traverser
 */
class AbstractTraverserTest extends \PHPUnit\Framework\TestCase
{

    protected $builder;

    protected function setUp():void
    {
        $this->builder = $this->getMockForAbstractClass(\Trismegiste\Mondrian\Builder\Compiler\AbstractTraverser::class);
    }

    public function testTraverser()
    {
        $obj = $this->builder->buildTraverser($this->createMock(\Trismegiste\Mondrian\Visitor\FqcnHelper::class));
        $this->assertInstanceOf(\PhpParser\NodeTraverser::class, $obj);
    }

    public function testWithDirector()
    {
        $visitor = $this->createMock(\Trismegiste\Mondrian\Visitor\FqcnHelper::class);

        $this->builder
                ->expects($this->once())
                ->method('buildCollectors')
                ->will($this->returnValue(array($visitor)));
        $visitor
                ->expects($this->once())
                ->method('enterNode');

        $director = new Director($this->builder);
        $director->compile(array(new PhpFile('abc', array())));
    }

}