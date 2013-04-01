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
        preg_match('#(.+)\\\\([^\\\\]+)$#', $this->name, $capt);
        $prefix = '';
        foreach (explode('\\', $capt[1]) as $part) {
            $prefix .= $part[0];
        }

        $default = array('shape' => 'circle', 'style' => 'filled',
            'color' => 'red', 'label' => $prefix . "\n" . $capt[2]);

        return $default;
    }

}