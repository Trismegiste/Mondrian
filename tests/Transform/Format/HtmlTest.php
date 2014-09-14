<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Transform\Format;

use Trismegiste\Mondrian\Transform\Format\Html;

/**
 * HtmlTest is a test for Html decorator for digraph
 */
class HtmlTest extends \PHPUnit_Framework_TestCase
{

    public function testEmpty()
    {
        $exporter = new Html(new NotPlanar());
        $content = $exporter->export();
        $this->assertStringStartsWith('<!DOCTYPE html>', $content);
    }

}

