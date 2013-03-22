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
        return array('shape' => 'triangle', 'style' => 'filled',
            'color' => 'yellow', 'label' => $capt[1]);
    }

}