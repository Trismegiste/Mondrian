<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Builder;

use Trismegiste\Mondrian\Builder\Linking;

/**
 * LinkingTest tests the facade for compilers
 */
class LinkingTest extends \PHPUnit_Framework_TestCase
{

    protected $facade;
    protected $parser;
    protected $compiler;

    protected function setUp()
    {
        $this->parser = $this->getMock('Trismegiste\Mondrian\Builder\Statement\BuilderInterface');
        $this->compiler = $this->getMock('Trismegiste\Mondrian\Builder\Compiler\BuilderInterface');
        $this->facade = new Linking($this->parser, $this->compiler);
    }

    public function testRun()
    {
        $this->parser
                ->expects($this->once())
                ->method('getParsed')
                ->will($this->returnValue(array()));

        $this->compiler
                ->expects($this->once())
                ->method('buildContext');

        $this->compiler
                ->expects($this->once())
                ->method('buildCollectors')
                ->will($this->returnValue(array()));

        $this->facade->run($this->getMock('Iterator'));
    }

}