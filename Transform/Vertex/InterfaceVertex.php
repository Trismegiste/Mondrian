<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform\Vertex;

/**
 * InterfaceVertex is a vertex for an interface
 */
class InterfaceVertex extends StaticAnalysis
{

    protected function getSpecific()
    {
        $capt = array();
        preg_match('#(.+)\\\\([^\\\\]+)$#', $this->name, $capt);
        $prefix = '';
        foreach (explode('\\', $capt[1]) as $part) {
            $prefix .= $part[0];
        }

        $default = array('shape' => 'invtriangle', 'style' => 'filled',
            'color' => 'green', 'label' => $prefix . "\n" . $capt[2]);

        return $default;
    }

}