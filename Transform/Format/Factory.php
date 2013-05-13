<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform\Format;

use Trismegiste\Mondrian\Graph\Graph;

/**
 * Factory is a simple factory for export format for Graph
 */
class Factory
{

    protected $typeList = array(
        'dot' => 'Graphviz',
        'json' => 'Json'
    );

    public function create(Graph $g, $format)
    {
        if (array_key_exists($format, $this->typeList)) {
            $classname = __NAMESPACE__ . '\\' . $this->typeList[$format];
            return new $classname($g);
        }

        throw new \InvalidArgumentException("Format $format is inknown");
    }

}
