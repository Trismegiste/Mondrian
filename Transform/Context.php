<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform;

use Trismegiste\Mondrian\Graph\Graph;

/**
 * Context is a context of parser
 *
 * @author flo
 */
class Context
{

    public $graph;
    public $inheritanceMap;
    public $vertex;

    public function __construct(Graph $g)
    {
        $this->graph = $g;
        $this->vertex = array('class' => array(), 'interface' => array(),
            'method' => array(), 'impl' => array(),
            'param' => array()
        );
        $this->inheritanceMap = array();
    }

}