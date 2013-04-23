<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Refactor;

use Trismegiste\Mondrian\Refactor\Contractor;
use Symfony\Component\Finder\Tests\Iterator\MockSplFileInfo;
use Symfony\Component\Finder\Tests\Iterator\MockFileListIterator;

/**
 * DoNothingTest is an full functional test for Contractor
 * do nothing because there is no annotations
 */
class DoNothingTest extends ContractorTestCase
{

    protected function setUp()
    {
        $this->initStorage(array('Nothing.php'));
        $this->createContractorMock(0);
    }

    /**
     * Validates the generation of refactored classes
     */
    public function testGeneration()
    {
        $this->coder->refactor($this->storage);
        $this->assertCount(1, $this->storage);
        $this->compileStorage();
        $this->assertTrue(class_exists('Refact\Nothing', false));
        $this->assertTrue(interface_exists('Refact\ForCodeCoverage', false));
    }

}