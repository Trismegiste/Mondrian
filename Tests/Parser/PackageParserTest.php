<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Parser;

use Trismegiste\Mondrian\Parser\PackageParser;

/**
 * PackageParserTest tests a parser of Package
 */
class PackageParserTest extends \PHPUnit_Framework_TestCase
{

    protected $package;
    protected $parser;

    protected function setUp()
    {
        $this->parser = $this->getMockBuilder('PHPParser_Parser')
                ->disableOriginalConstructor()
                ->setMethods(array('parse'))
                ->getMock();
        $this->package = new PackageParser($this->parser);
    }

    public function getListing()
    {
        return array(array(array(
                            $this->getMockBuilder('Symfony\Component\Finder\SplFileInfo')
                            ->disableOriginalConstructor()
                            ->setMethods(array('getRealPath', 'getContents'))
                            ->getMock()
        )));
    }

    /**
     * @dataProvider getListing
     */
    public function testScanning($listing)
    {
        $listing[0]
                ->expects($this->once())
                ->method('getRealPath')
                ->will($this->returnValue('abc'));
        $listing[0]
                ->expects($this->once())
                ->method('getContents')
                ->will($this->returnValue('dummy'));

        $this->parser
                ->expects($this->once())
                ->method('parse')
                ->will($this->returnValue(array()));

        $ret = $this->package->parse(new \ArrayIterator($listing));

        $this->assertCount(1, $ret);
        $this->assertInstanceOf('Trismegiste\Mondrian\Parser\PhpFile', $ret[0]);
    }

}