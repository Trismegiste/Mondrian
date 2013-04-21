<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Refactor;

use Trismegiste\Mondrian\Refactor\Contractor;

/**
 * ContractorTest is an almost full functional test 
 * for Contractor
 */
class ContractorTest extends \PHPUnit_Framework_TestCase
{

    protected $coder;
    protected $storage;

    public function stubbedWrite($fch, array $stmts)
    {
        $prettyPrinter = new \PHPParser_PrettyPrinter_Default();
        $this->storage[$fch] = "<?php\n\n" . $prettyPrinter->prettyPrint($stmts);
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
        $generated = '';
        foreach ($this->storage as $str) {
            $str = preg_replace('#^<\?php#', '', $str);
            if (!empty($generated)) {
                $str = preg_replace('#^namespace.+$#m', '', $str);
            }
            $generated .= $str;
        }
        eval($generated);
        $this->assertTrue(class_exists('Refact\Earth', false));
        $this->assertTrue(class_exists('Refact\Moon', false));
        $this->assertTrue(interface_exists('Refact\EarthInterface', false));
        $this->assertTrue(interface_exists('Refact\MoonInterface', false));
        // testing refactored
        $earth = new \Refact\Earth();
        $moon = new \Refact\Moon();
        $this->assertEquals('Fly me to the Moon', $earth->attract($moon));
        $this->assertEquals('Circling around the Earth', $moon->orbiting($earth));
    }

}