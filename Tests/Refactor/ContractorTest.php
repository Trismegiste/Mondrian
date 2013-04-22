<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Refactor;

use Trismegiste\Mondrian\Refactor\Contractor;
use Symfony\Component\Finder\Tests\Iterator\MockSplFileInfo;
use Symfony\Component\Finder\Tests\Iterator\MockFileListIterator;

/**
 * ContractorTest is an almost full functional test 
 * for Contractor
 */
class ContractorTest extends \PHPUnit_Framework_TestCase
{

    protected $coder;
    protected $storage;

    /**
     * Stub for writes
     * @param string $fch
     * @param array $stmts 
     */
    public function stubbedWrite($fch, array $stmts)
    {
        $prettyPrinter = new \PHPParser_PrettyPrinter_Default();
        $this->storage[basename($fch)] = new MockSplFileInfo(
                        array(
                            'name' => $fch,
                            'contents' => "<?php\n\n" . $prettyPrinter->prettyPrint($stmts)
                        )
        );
    }

    /**
     * Init VFS
     * 
     * @return int how many files ?
     */
    protected function initStorage()
    {
        $fileSystem = array('Earth.php', 'Moon.php');

        $iter = array();
        foreach ($fileSystem as $name) {
            $absolute = __DIR__ . '/../Fixtures/Refact/' . $name;
            $iter[$name] = array(
                'name' => $absolute,
                'contents' => file_get_contents($absolute)
            );
        }
        $this->storage = new MockFileListIterator($iter);

        return count($fileSystem);
    }

    protected function setUp()
    {
        $cpt = 3 * $this->initStorage(); // 3 passes 

        $this->coder = $this->getMockBuilder('Trismegiste\Mondrian\Refactor\Contractor')
                ->setMethods(array('writeStatement', 'readFile'))
                ->getMock();
        $this->coder
                ->expects($this->exactly($cpt))
                ->method('writeStatement')
                ->will($this->returnCallback(array($this, 'stubbedWrite')));
    }

    /**
     * Compile VFS
     */
    protected function compileStorage()
    {
        $generated = '';
        foreach ($this->storage as $fch) {
            $str = preg_replace('#^<\?php#', '', $fch->getContents());
            if (!empty($generated)) {
                $str = preg_replace('#^namespace.+$#m', '', $str);
            }
            $generated .= $str;
        }
        eval($generated);
    }

    /**
     * Validates the generation of refactored classes
     */
    public function testGeneration()
    {
        $this->coder->refactor($this->storage);
        $this->compileStorage();
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