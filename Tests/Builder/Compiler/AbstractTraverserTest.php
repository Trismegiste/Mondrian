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
class AbstractTraverserTest extends \PHPUnit_Framework_TestCase
{

    protected $builder;

    protected function setUp()
    {
        $this->builder = $this->getMockForAbstractClass('Trismegiste\Mondrian\Builder\Compiler\AbstractTraverser');
    }

    public function testTraverser()
    {
        $obj = $this->builder->buildTraverser($this->getMock('Trismegiste\Mondrian\Visitor\FqcnHelper'));
        $this->assertInstanceOf('PHPParser_NodeTraverser', $obj);
    }

    public function testWithDirector()
    {
        $visitor = $this->getMock('Trismegiste\Mondrian\Visitor\FqcnHelper');

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