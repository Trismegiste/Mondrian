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
        $default = array('shape' => 'invtriangle', 'style' => 'filled',
            'color' => 'green', 'label' => $this->compactFqcn($this->name));

        return $default;
    }

}
