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
class VirtualPhpDumper extends PhpDumper
{

    protected $storage;
    protected $testCase;

    /**
     * Init VFS
     */
    public function __construct(PHPUnit_Framework_TestCase $testCase, array $fileSystem)
    {
        $iter = array();
        $this->testCase = $testCase;

        foreach ($fileSystem as $name) {
            $absolute = __DIR__ . '/../Fixtures/Refact/' . $name;
            $iter[$name] = $this->getMockFile($absolute, file_get_contents($absolute));
        }
        $this->storage = new \ArrayIterator($iter);
    }

    protected function getMockFile($absolute, $content)
    {
        $builder = new \PHPUnit_Framework_MockObject_MockBuilder($this->testCase, 'Symfony\Component\Finder\SplFileInfo');
        $file = $builder->getMock();
        $file->expects($this->testCase->any())
                ->method('getRealPath')
                ->will($this->testCase->willReturn($absolute));
        $file->expects($this->testCase->any())
                ->method('getContents')
                ->will($this->testCase->willReturn($content));

        return $file;
    }

    /**
     * Stub for writes
     * 
     * @param \Trismegiste\Mondrian\Parser\PhpFile $file
     */
    public function write(PhpFile $file)
    {
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

}