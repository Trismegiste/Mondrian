<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform\Vertex;

/**
 * ParamVertex is a vertex for an parameter of a method
 */
class ParamVertex extends StaticAnalysis
{

    public function getAttribute()
    {
        preg_match('#([^/]+)$#', $this->name, $capt);
        return array( 'shape' => 'diamond',
            'style' => 'filled', 'color' => 'cyan', 'label' => $capt[1]);
    }

}