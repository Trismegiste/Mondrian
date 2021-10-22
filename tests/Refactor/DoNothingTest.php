<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Refactor;

use Trismegiste\Mondrian\Builder\Linking;
use Trismegiste\Mondrian\Builder\Statement\Builder;
use Trismegiste\Mondrian\Refactor\ContractorBuilder;

/**
 * DoNothingTest is an full functional test for ContractorBuilder
 * do nothing because there is no annotations
 */
class DoNothingTest extends RefactorTemplate
{

    protected function setUp():void
    {
        parent::setUp();
        $this->coder = new Linking(
                new Builder(), new ContractorBuilder($this->dumper));
    }

    /**
     * Validates the generation of refactored classes
     */
    public function testGeneration()
    {
        $this->dumper->init(array('Nothing.php'), $this->never());

        $this->coder->run($this->dumper->getIterator());
        $this->assertCount(1, $this->dumper->getIterator());
        $this->dumper->compileStorage();
        $this->assertTrue(class_exists('Refact\Nothing', false));
        $this->assertTrue(interface_exists('Refact\ForCodeCoverage', false));
    }

}
