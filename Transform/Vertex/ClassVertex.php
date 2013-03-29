<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform\Vertex;

/**
 * ClassVertex is a vertex for a class
 */
class ClassVertex extends StaticAnalysis
{

    public function getAttribute()
    {
        $capt = array();
        preg_match('#([^\\\\]+)$#', $this->name, $capt);
        $default = array('shape' => 'circle', 'style' => 'filled',
            'color' => 'red', 'label' => $capt[1]);

        if ($this->hasMeta('centrality')) {
            $default['color'] = sprintf('/rdylgn11/%d', $this->getMeta('centrality'));
        }

        return $default;
    }

}