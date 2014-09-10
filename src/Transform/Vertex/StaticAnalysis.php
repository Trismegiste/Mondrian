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

    const LABEL_DELIMITER  = '\n';

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
            $default['color'] = sprintf('%.3f,1,1', $this->getMeta('centrality') * 0.7);
        }

        return $default;
    }

    abstract protected function getSpecific();

    /**
     * Compacts a FQCN by shortening the full "path"
     *
     * @param string $str the FQCN
     * @return string a compacted form of the FQCN
     */
    protected function compactFqcn($str)
    {
        $parts = explode('\\', $str);
        $short = array_pop($parts);
        $prefix = '';
        foreach ($parts as $itm) {
            $prefix .= $itm[0];
        }
        if (!empty($prefix)) {
            $prefix .= self::LABEL_DELIMITER;
        }

        return $prefix . $short;
    }

}
