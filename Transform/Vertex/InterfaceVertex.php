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
        $default = array('shape' => 'invtriangle', 'style' => 'filled',
            'color' => 'green', 'label' => $capt[1]);

        if ($this->hasMeta('centrality')) {
            $default['color'] = sprintf('/rdylgn11/%d', $this->getMeta('centrality'));
        }

        return $default;
    }

}