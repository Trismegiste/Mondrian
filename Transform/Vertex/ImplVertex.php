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
        if ($this->hasMeta('depend')) {
            $default['color'] = sprintf('/rdylgn11/%.0f', 1 + $this->getMeta('depend'));
        }

        return $default;
    }

}