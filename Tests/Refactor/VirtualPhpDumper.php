<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Refactor;

use Trismegiste\Mondrian\Parser\PhpDumper;
use Trismegiste\Mondrian\Parser\PhpFile;

/**
 * VirtualPhpDumper is a stub for virtual php dumper
 */
class VirtualPhpDumper extends PhpDumper implements \IteratorAggregate
{

    protected $storage;
    protected $testCase;
    protected $directory;
    protected $invocationMocker;

    /**
     * Init VFS
     */
    public function __construct(\PHPUnit_Framework_TestCase $testCase, array $fileSystem)
    {
        $this->invocationMocker = new \PHPUnit_Framework_MockObject_InvocationMocker();
        $this->invocationMocker
                ->expects($testCase->exactly(1))
                ->method('write');

        $this->storage = array();
        $this->testCase = $testCase;
        $this->directory = __DIR__ . '/../Fixtures/Refact/';

        foreach ($fileSystem as $name) {
            $absolute = $this->directory . $name;
            $this->storage[$name] = $this->getMockFile($absolute, file_get_contents($absolute));
        }
    }

    protected function getMockFile($absolute, $content)
    {
        $file = $this->testCase
                ->getMockBuilder('Symfony\Component\Finder\SplFileInfo')
                ->disableOriginalConstructor()
                ->getMock();
        $file->expects($this->testCase->any())
                ->method('getRealPath')
                ->will($this->testCase->returnValue($absolute));
        $file->expects($this->testCase->any())
                ->method('getContents')
                ->will($this->testCase->returnValue($content));

        return $file;
    }

    /**
     * Stub for writes
     * 
     * @param \Trismegiste\Mondrian\Parser\PhpFile $file
     */
    public function write(PhpFile $file)
    {
        $this->invocationMocker->invoke(
                new \PHPUnit_Framework_MockObject_Invocation_Object(
                __CLASS__, 'write', array(), $this
                )
        );

        $fch = $file->getRealPath();
        $stmts = iterator_to_array($file->getIterator());
        $prettyPrinter = new \PHPParser_PrettyPrinter_Default();
        $this->storage[basename($fch)] = $this->getMockFile(
                $fch, "<?php\n\n" . $prettyPrinter->prettyPrint($stmts)
        );
    }

    /**
     * Compile VFS
     */
    public function compileStorage()
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

    public function getIterator()
    {
        return new \ArrayIterator($this->storage);
    }

    public function verifyCalls()
    {
        $this->invocationMocker->verify();
    }

}