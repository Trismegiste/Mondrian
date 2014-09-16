<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Transform\Format;

use Trismegiste\Mondrian\Transform\Format\Json;

/**
 * JsonTest tests the Json
 */
class JsonTest extends \PHPUnit_Framework_TestCase
{

    public function testSimple()
    {
        $exporter = new Json(new NotPlanar());
        $content = json_decode($exporter->export(), true);
        $this->assertArrayHasKey('nodes', $content);
        $this->assertArrayHasKey('links', $content);
    }

}