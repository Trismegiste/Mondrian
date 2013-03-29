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
        $default = array('shape' => 'rectangle', 'style' => 'filled', 'label' => $capt[1]);
        if ($this->hasMeta('centrality')) {
            $default['color'] = sprintf('/rdylgn11/%d', $this->getMeta('centrality'));
        }

        return $default;
    }

}