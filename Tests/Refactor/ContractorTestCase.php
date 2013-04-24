<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Refactor;

use Trismegiste\Mondrian\Refactor\Contractor;
use Symfony\Component\Finder\Tests\Iterator\MockSplFileInfo;
use Symfony\Component\Finder\Tests\Iterator\MockFileListIterator;

/**
 * ContractorTestCase is an abstract full functional test 
 * for Contractor
 */
abstract class ContractorTestCase extends \PHPUnit_Framework_TestCase
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
    protected function initStorage($fileSystem)
    {
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

    protected function createContractorMock($cpt)
    {
        $this->coder = $this->getMockBuilder('Trismegiste\Mondrian\Refactor\Contractor')
                ->setMethods(array('writeStatement'))
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

}