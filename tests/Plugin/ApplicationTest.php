<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Plugin;

use Trismegiste\Mondrian\Plugin\Application;

/**
 * ApplicationTest is the Application class with plugin
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{

    protected $sut;

    protected function setUp()
    {
        $this->sut = new Application();
    }

    public function testAddPlugin()
    {
        $fqcn = __NAMESPACE__ . '\FakeCommand';
        $this->sut->addPlugin([$fqcn]);

        $this->assertInstanceOf($fqcn, $this->sut->get('fake'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidPlugin()
    {
        $this->sut->addPlugin(['stdClass']);
    }

}