<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform\Vertex;

/**
 * MethodVertex is a vertex for a method
 */
class MethodVertex extends StaticAnalysis
{

    public function getAttribute()
    {
        preg_match('#([^:]+)$#', $this->name, $capt);
        $default = array('shape' => 'triangle', 'style' => 'filled',
            'color' => 'yellow', 'label' => $capt[1]);

        if ($this->hasMeta('centrality')) {
            $default['color'] = sprintf('/rdylgn11/%d', $this->getMeta('centrality'));
        }

        return $default;
    }

}