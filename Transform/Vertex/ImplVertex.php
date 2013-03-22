<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform\Vertex;

/**
 * ImplVertex is a vertex for an implementation
 * (content of a method)
 */
class ImplVertex extends StaticAnalysis
{

    public function getAttribute()
    {
        preg_match('#([^:]+)$#', $this->name, $capt);
        return array('shape' => 'rectangle', 'label' => $capt[1]);
    }

}