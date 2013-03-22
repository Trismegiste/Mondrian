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
        return array('shape' => 'circle', 'style' => 'filled',
            'color' => 'red', 'label' => $capt[1]);
    }

}