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
        preg_match('#([^\\\\]+)$#', $this->name, $capt);
        $default = array('shape' => 'invtriangle', 'style' => 'filled',
            'color' => 'green', 'label' => $capt[1]);

        return $default;
    }

}