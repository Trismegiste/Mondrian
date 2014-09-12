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

    protected function getSpecific()
    {
        preg_match('#([^\\\\]+)::([^:]+)$#', $this->name, $capt);
        $default = array('shape' => 'rectangle', 'style' => 'filled', 'color' => 'grey',
            'label' => $capt[1] . self::LABEL_DELIMITER . $capt[2]);

        return $default;
    }

}
