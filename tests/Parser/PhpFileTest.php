<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Parser;

use Trismegiste\Mondrian\Parser\PhpFile;

/**
 * PhpFileTest tests PhpFile
 */
class PhpFileTest extends \PHPUnit\Framework\TestCase
{

    protected $obj;

    protected function setUp():void
    {
        $this->obj = new PhpFile('abc', array());
    }

    public function testDefault()
    {
        $this->assertEquals('abc', $this->obj->getRealPath());
        $this->assertEquals('PhpFile', $this->obj->getType());
        $this->assertFalse($this->obj->isModified());
    }

    public function testModified()
    {
        $this->assertFalse($this->obj->isModified());
        $this->obj->modified();
        $this->assertTrue($this->obj->isModified());
    }

}
