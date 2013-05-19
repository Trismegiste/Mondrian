<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Builder\Statement;

use Trismegiste\Mondrian\Builder\Statement\Director;

/**
 * DirectorTest tests the director that builds the Parser with the help of the builder
 *
 * @author flo
 */
class DirectorTest extends \PHPUnit_Framework_TestCase
{

    protected $director;
    protected $builder;

    protected function setUp()
    {
        $this->builder = $this->getMock('Trismegiste\Mondrian\Builder\Statement\BuilderInterface');
        $this->director = new Director($this->builder);
    }

    public function testBuilding()
    {
        $this->builder
                ->expects($this->once())
                ->method('buildLexer');
        $this->builder
                ->expects($this->once())
                ->method('buildFileLevel');
        $this->builder
                ->expects($this->once())
                ->method('buildPackageLevel');
        $this->builder
                ->expects($this->once())
                ->method('getParsed');

        $this->director->parse($this->getMock('Iterator'));
    }

}