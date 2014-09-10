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

    protected function getSpecific()
    {
        preg_match('#([^/]+)$#', $this->name, $capt);
        $default = array('shape' => 'diamond',
            'style' => 'filled', 'color' => 'cyan', 'label' => $capt[1]);

        return $default;
    }

}
