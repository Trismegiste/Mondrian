<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform\Format;

/**
 * Html is an exporter to Html + Json + d3.js format 
 * 
 * Do not require Graphviz
 */
class Html extends Json
{

    public function export()
    {
        $d3js = file_get_contents(__DIR__ . '/d3.min.js');
        $template = file_get_contents(__DIR__ . '/template.html');
        $graph = parent::export();

        return str_replace(['__D3JS__', '__GRAPH__'], [$d3js, $graph], $template);
    }

}
