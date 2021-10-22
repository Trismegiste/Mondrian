<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Refactor;

use Trismegiste\Mondrian\Builder\Linking;
use Trismegiste\Mondrian\Builder\Statement\Builder;
use Trismegiste\Mondrian\Refactor\FactoryGenBuilder;

/**
 * ParseAndFactoryTest is an almost full functional test
 * for FactoryGenBuilder
 */
class ParseAndFactoryTest extends RefactorTemplate
{

    protected function setUp():void
    {
        parent::setUp();
        $this->coder = new Linking(
                new Builder(), new FactoryGenBuilder($this->dumper));
    }

    /**
     * Validates the generation of refactored classes
     */
    public function testGeneration()
    {
        $this->dumper->init(array('ForFactory.php'), $this->once());
        $this->coder->run($this->dumper->getIterator());
        $this->dumper->compileStorage();
        $this->assertTrue(class_exists('Refact\ForFactory', false));
        $refl = new \ReflectionClass('\Refact\ForFactory');
        $this->assertTrue($refl->hasMethod('createMoon0'));
    }

}
