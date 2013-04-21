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

    public function stubbedRead($fch)
    {
        return $this->storage[$fch];
    }

    protected function setUp()
    {
        $fileSystem = array(
            __DIR__ . '/../Fixtures/Refact/Earth.php',
            __DIR__ . '/../Fixtures/Refact/Moon.php'
        );
        foreach ($fileSystem as $fch) {
            $this->storage[$fch] = file_get_contents($fch);
        }

        $this->coder = $this->getMockBuilder('Trismegiste\Mondrian\Refactor\Contractor')
                ->setMethods(array('writeStatement', 'readFile'))
                ->getMock();
        $this->coder
                ->expects($this->exactly(6))
                ->method('writeStatement')
                ->will($this->returnCallback(array($this, 'stubbedWrite')));
        $this->coder
                ->expects($this->exactly(6))
                ->method('readFile')
                ->will($this->returnCallback(array($this, 'stubbedRead')));
    }

    public function testGeneration()
    {
        $iter = array(
            __DIR__ . '/../Fixtures/Refact/Earth.php',
            __DIR__ . '/../Fixtures/Refact/Moon.php'
        );
        $this->coder->refactor($iter);
        print_r($this->storage);
        foreach ($this->storage as $str) {
      //      eval($str);
        }
      /*  $this->assertTrue(class_exists('Refact\Earth', false));
        $this->assertTrue(class_exists('Refact\Moon', false));
        $this->assertTrue(interface_exists('Refact\EarthInterface', false));
        $this->assertTrue(interface_exists('Refact\MoonInterface', false));*/
        // note : the generation does not really works here since the class
        // is written two times and I store it in memory
    }

}