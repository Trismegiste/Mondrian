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

    protected function getSpecific()
    {
        preg_match('#([^:]+)$#', $this->name, $capt);
        $default = array('shape' => 'triangle', 'style' => 'filled',
            'color' => 'yellow', 'label' => $capt[1]);

        return $default;
    }

}