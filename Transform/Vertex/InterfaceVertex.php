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

    public function getAttribute()
    {
        preg_match('#([^\\\\]+)$#', $this->name, $capt);
        return array('shape' => 'invtriangle', 'style' => 'filled',
            'color' => 'green', 'label' => $capt[1]);
    }

}