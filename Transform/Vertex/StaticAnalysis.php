<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform\Vertex;

use Trismegiste\Mondrian\Graph\Vertex;

/**
 * StaticAnalysis is a vertex for analysis of code
 */
abstract class StaticAnalysis extends Vertex implements Vizable, MetaInterface
{

    protected $metadata = array();

    public function getMeta($key)
    {
        return $this->metadata[$key];
    }

    public function setMeta($key, $val)
    {
        $this->metadata[$key] = $val;
    }

    public function hasMeta($key)
    {
        return array_key_exists($key, $this->metadata);
    }

    public function getAttribute()
    {
        $default = $this->getSpecific();
        
        if ($this->hasMeta('centrality')) {
            $default['color'] = sprintf('/rdylgn11/%d', $this->getMeta('centrality'));
        }

        return $default;
    }

    abstract protected function getSpecific();
}