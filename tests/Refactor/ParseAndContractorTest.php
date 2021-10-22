<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Refactor;

use Trismegiste\Mondrian\Builder\Linking;
use Trismegiste\Mondrian\Builder\Statement\Builder;
use Trismegiste\Mondrian\Refactor\ContractorBuilder;

/**
 * ParseAndContractorTest is an almost full functional test
 * for ContractorBuilder
 */
class ParseAndContractorTest extends RefactorTemplate
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
        $this->dumper->init(array('Earth.php', 'Moon.php'), $this->exactly(4));

        $this->coder->run($this->dumper->getIterator());
        $this->dumper->compileStorage();
        $this->assertTrue(class_exists('Refact\Earth', false));
        $this->assertTrue(class_exists('Refact\Moon', false));
        $this->assertTrue(interface_exists('Refact\EarthInterface', false));
        $this->assertTrue(interface_exists('Refact\MoonInterface', false));
        // testing refactored
        $earth = new \Refact\Earth();
        $this->assertInstanceOf('Refact\EarthInterface', $earth);
        $moon = new \Refact\Moon();
        $this->assertInstanceOf('Refact\MoonInterface', $moon);
        $this->assertEquals('Fly me to the Moon', $earth->attract($moon));
        $this->assertEquals('Circling around the Earth', $moon->orbiting($earth));
    }

}
