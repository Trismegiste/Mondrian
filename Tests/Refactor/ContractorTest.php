<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Refacotr;

use Trismegiste\Mondrian\Refactor\Contractor;

/**
 * ContractorTest is test for Contractor
 *
 */
class ContractorTest extends \PHPUnit_Framework_TestCase
{

    protected $coder;
    protected $storage;

    public function stubbedWrite($fch, array $stmts)
    {
        $prettyPrinter = new \PHPParser_PrettyPrinter_Default();
        $this->storage[$fch] = $prettyPrinter->prettyPrint($stmts);
    }

    protected function setUp()
    {
        $this->storage = array();

        $this->coder = $this->getMockBuilder('Trismegiste\Mondrian\Refactor\Contractor')
                ->setMethods(array('writeStatement'))
                ->getMock();
        $this->coder
                ->expects($this->exactly(6))
                ->method('writeStatement')
                ->will($this->returnCallback(array($this, 'stubbedWrite')));
    }

    public function testGeneration()
    {
        $iter = array(
            __DIR__ . '/../Fixtures/Refact/Earth.php',
            __DIR__ . '/../Fixtures/Refact/Moon.php'
        );
        $this->coder->refactor($iter);
        foreach ($this->storage as $str) {
            eval($str);
        }
        print_r($this->storage);
        $this->assertTrue(class_exists('Refact\Earth', false));
        $this->assertTrue(class_exists('Refact\Moon', false));
        $this->assertTrue(interface_exists('Refact\EarthInterface', false));
        $this->assertTrue(interface_exists('Refact\MoonInterface', false));
        // note : the generation does not really works here since the class
        // is written two times and I store it in memory
    }

}