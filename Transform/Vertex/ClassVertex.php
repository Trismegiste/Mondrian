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

    protected function getSpecific()
    {
        $capt = array();
        preg_match('#([^\\\\]+)$#', $this->name, $capt);
        $default = array('shape' => 'circle', 'style' => 'filled',
            'color' => 'red', 'label' => $capt[1]);

        return $default;
    }

}