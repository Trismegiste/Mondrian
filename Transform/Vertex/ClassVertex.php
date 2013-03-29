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

        if ($this->hasMeta('depend')) {
            $default['color'] = sprintf('/spectral11/%.0f', 1 + $this->getMeta('depend'));
        }

        return $default;
    }

}