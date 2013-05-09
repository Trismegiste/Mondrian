<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Config;

use Trismegiste\Mondrian\Config\Helper;

/**
 * HelperTest tests the helper of config
 */
class HelperTest extends \PHPUnit_Framework_TestCase
{

    public function testDefaultCfg()
    {
        $helper = new Helper();
        $cfg = $helper->getConfig(__DIR__);
        $this->assertArrayHasKey('graph', $cfg);
        $this->assertArrayHasKey('calling', $cfg['graph']);
    }

    public function testLoad()
    {
        $helper = new Helper();
        $cfg = $helper->getConfig(dirname(__DIR__) . '/Fixtures');
        $this->assertArrayHasKey('SomeClass::someMethod', $cfg['graph']['calling']);
    }

}