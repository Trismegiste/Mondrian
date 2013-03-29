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
        $default = array('shape' => 'diamond',
            'style' => 'filled', 'color' => 'cyan', 'label' => $capt[1]);

        if ($this->hasMeta('centrality')) {
            $default['color'] = sprintf('/rdylgn11/%d', $this->getMeta('centrality'));
        }

        return $default;
    }

}