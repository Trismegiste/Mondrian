<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Parser;

use Trismegiste\Mondrian\Parser\BuilderFactory;

/**
 * BuilderFactoryTest tests the enhanced builder factory with PhpFile node 
 */
class BuilderFactoryTest extends \PHPUnit\Framework\TestCase
{

    protected $factory;

    protected function setUp():void
    {
        $this->factory = new BuilderFactory();
    }

    public function testCreatesBuilder()
    {
        $builder = $this->factory->file('abc.php');
        $this->assertInstanceOf('Trismegiste\Mondrian\Parser\PhpFileBuilder', $builder);
        $default = $builder->getNode();
        $this->assertInstanceOf('Trismegiste\Mondrian\Parser\PhpFile', $default);
        $this->assertEquals('abc.php', $default->getRealPath());
    }

}