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

    public function testLoad()
    {
        $helper = new Helper();
        $cfg = $helper->getConfig(dirname(__DIR__) . '/Fixtures');
        $this->assertArrayHasKey('SomeClass::someMethod', $cfg['graph']['calling']);
    }

}