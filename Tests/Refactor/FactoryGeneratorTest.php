<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Refactor;

use Symfony\Component\Finder\Tests\Iterator\MockSplFileInfo;
use Symfony\Component\Finder\Tests\Iterator\MockFileListIterator;
use Trismegiste\Mondrian\Parser\PhpFile;

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

//        $this->dumper
//                ->expects($this->once())
//                ->method('write')
//                ->will($this->returnCallback(array($this, 'stubbedWrite')));

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