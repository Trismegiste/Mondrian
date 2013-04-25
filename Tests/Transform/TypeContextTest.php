<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Transform;

use Trismegiste\Mondrian\Transform\Context;

/**
 * TypeContextTest tests for inheritance Context
 */
class TypeContextTest extends \PHPUnit_Framework_TestCase
{

    protected $context;

    protected function setUp()
    {
        $this->context = new Context();
    }

    public function testEmpty()
    {
        $this->assertFalse($this->context->hasDeclaringClass('unknown'));
    }

    public function testInitClass()
    {
        $this->context->initSymbol('Some', false);
        $this->assertTrue($this->context->hasDeclaringClass('Some'));
        $this->assertFalse($this->context->isInterface('Some'));
    }

    public function testInitInterface()
    {
        $this->context->initSymbol('Some', true);
        $this->assertTrue($this->context->hasDeclaringClass('Some'));
        $this->assertTrue($this->context->isInterface('Some'));
    }

    public function testSimpleMethod()
    {
        $this->context->initSymbol('Type', false);
        $this->context->addMethodToClass('Type', 'sample');
        $this->assertEquals('Type', $this->context->getDeclaringClass('Type', 'sample'));
        $this->context->resolveSymbol();
        $this->assertEquals('Type', $this->context->getDeclaringClass('Type', 'sample'));
    }

    public function testSimpleInheritance()
    {
        $this->context->initSymbol('Class', false);
        $this->context->addMethodToClass('Class', 'sample');
        $this->context->initSymbol('Interface', true);
        $this->context->addMethodToClass('Interface', 'sample');
        $this->context->resolveSymbol();
        $this->assertEquals('Interface', $this->context->getDeclaringClass('Interface', 'sample'));
        $this->assertEquals('Class', $this->context->getDeclaringClass('Class', 'sample'));
        // add inheritance :
        $this->context->pushParentClass('Class', 'Interface');
        $this->context->resolveSymbol();
        $this->assertEquals('Interface', $this->context->getDeclaringClass('Interface', 'sample'));
        $this->assertEquals('Interface', $this->context->getDeclaringClass('Class', 'sample'));
    }

    public function testSuperClass()
    {
        $this->context->initSymbol('Class', false);
        $this->context->initSymbol('Mother', false);
        $this->context->addMethodToClass('Mother', 'sample');
        $this->context->pushParentClass('Class', 'Mother');
        $this->context->resolveSymbol();
        $this->assertEquals('Mother', $this->context->findMethodInInheritanceTree('Class', 'sample'));
    }

}