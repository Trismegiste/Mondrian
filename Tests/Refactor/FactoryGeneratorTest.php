<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Refactor;

/**
 * FactoryGeneratorTest is an almost full functional test
 * for FactoryGenerator
 */
class FactoryGeneratorTest extends \PHPUnit_Framework_TestCase
{

    protected $coder;
    protected $dumper;

    protected function setUp()
    {
        $this->dumper = new VirtualPhpDumper($this, __DIR__ . '/../Fixtures/Refact/');
        $this->coder = new \Trismegiste\Mondrian\Refactor\FactoryGenerator($this->dumper);
    }

    /**
     * Validates the generation of refactored classes
     */
    public function testGeneration()
    {
        $this->dumper->init(array('ForFactory.php'), $this->once());
        $this->coder->refactor($this->dumper->getIterator());
        $this->dumper->compileStorage();
        $this->assertTrue(class_exists('Refact\ForFactory', false));
    }

    protected function verifyMockObjects()
    {
        parent::verifyMockObjects();
        $this->dumper->verifyCalls();
    }

}