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

        if ($this->hasMeta('depend')) {
            $default['color'] = sprintf('/rdylgn11/%.0f', 1 + $this->getMeta('depend'));
        }

        return $default;
    }

}