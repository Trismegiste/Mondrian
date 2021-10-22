<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Parser;

use Trismegiste\Mondrian\Parser\PhpFileBuilder;

/**
 * PhpFileBuilderTest test the builder of PhpFile
 */
class PhpFileBuilderTest extends \PHPUnit\Framework\TestCase
{

    protected $builder;

    protected function setUp():void
    {
        $this->builder = new PhpFileBuilder('abc.php');
    }

    public function testEmpty()
    {
        $file = $this->builder->getNode();
        $this->assertInstanceOf('Trismegiste\Mondrian\Parser\PhpFile', $file);
        $this->assertEquals('abc.php', $file->getRealPath());
    }

    public function testNamespace()
    {
        $file = $this->builder->ns('Vertex')->getNode();
        $ns = $file->getIterator()->current();
        $this->assertEquals('Vertex', (string) $ns->name);
    }

    public function testUsing()
    {
        $file = $this->builder->addUse('Nice')->addUse('Sprites')->getNode();
        $using = iterator_to_array($file->getIterator());
        $this->assertEquals('Nice', (string) $using[0]->uses[0]->name);
        $this->assertEquals('Sprites', (string) $using[1]->uses[0]->name);
    }

    public function testClass()
    {
        $file = $this->builder
                ->declaring(new \PHPParser_Node_Stmt_Class('Scary'))
                ->getNode();
        $cls = $file->getIterator()->current();
        $this->assertEquals('Scary', (string) $cls->name);
    }

    public function testOnlyOneClass()
    {
        $file = $this->builder
                ->declaring(new \PHPParser_Node_Stmt_Class('Scary'))
                ->declaring(new \PHPParser_Node_Stmt_Class('Monsters'))
                ->getNode();
        $cls = $file->getIterator()->current();
        $this->assertEquals('Monsters', (string) $cls->name);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Stmt_ClassMethod
     */
    public function testInvalidNodeThrowsException()
    {
        $file = $this->builder
                ->declaring(new \PHPParser_Node_Stmt_ClassMethod('Fail'))
                ->getNode();
    }

}