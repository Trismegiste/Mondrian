<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Parser;

use Trismegiste\Mondrian\Parser\PackageParser;

/**
 * PackageParserTest tests a parser of Package
 */
class PackageParserTest extends \PHPUnit\Framework\TestCase
{

    protected $package;
    protected $parser;

    protected function setUp():void
    {
        $this->parser = $this->getMockBuilder('PHPParser_Parser')
                ->disableOriginalConstructor()
                ->setMethods(array('parse'))
                ->getMock();
        $this->package = new PackageParser($this->parser);
    }

    public function getListing()
    {
        return [[[new \Trismegiste\Mondrian\Tests\Fixtures\MockSplFileInfo('abc', 'dummy')]]];
    }

    /**
     * @dataProvider getListing
     */
    public function testScanning($listing)
    {
        $this->parser
                ->expects($this->once())
                ->method('parse')
                ->with($this->equalTo('dummy'))
                ->will($this->returnValue(array()));

        $ret = $this->package->parse(new \ArrayIterator($listing));

        $this->assertCount(1, $ret);
        $this->assertInstanceOf('Trismegiste\Mondrian\Parser\PhpFile', $ret[0]);
    }

}
