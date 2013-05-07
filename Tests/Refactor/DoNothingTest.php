<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Refactor;

use Trismegiste\Mondrian\Refactor\Contractor;

/**
 * DoNothingTest is an full functional test for Contractor
 * do nothing because there is no annotations
 */
class DoNothingTest extends RefactorTemplate
{

    protected function setUp()
    {
        parent::setUp();
        $this->coder = new Contractor($this->dumper);
    }

    /**
     * Validates the generation of refactored classes
     */
    public function testGeneration()
    {
        $this->dumper->init(array('Nothing.php'), $this->never());

        $this->coder->refactor($this->dumper->getIterator());
        $this->assertCount(1, $this->dumper->getIterator());
        $this->dumper->compileStorage();
        $this->assertTrue(class_exists('Refact\Nothing', false));
        $this->assertTrue(interface_exists('Refact\ForCodeCoverage', false));
    }

}