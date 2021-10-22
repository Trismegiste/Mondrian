<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Transform\Format;

use Trismegiste\Mondrian\Transform\Format\Svg;
use Trismegiste\Mondrian\Graph\Digraph;

/**
 * SvgTest is a test for SvgTest decorator
 */
class SvgTest extends \PHPUnit\Framework\TestCase
{

    public function testExists()
    {
        $exporter = new Svg(new Digraph());
        try {
            $exporter->export();
        } catch (\Exception $e) {
            
        }
    }

}

