<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform\Format;

use Trismegiste\Mondrian\Transform\Vertex\StaticAnalysis;

/**
 * Json is a decorator for JSON output
 *
 */
class Json extends GraphExporter
{

    private function shortenClassname($cls)
    {
        $ns = explode('\\', $cls);
        $short = array_pop($ns);
        $prefix = '';
        foreach ($ns as $item) {
            $prefix .= $item[0];
        }

        return $prefix . '\\' . $short;
    }

    private function exportVertex(StaticAnalysis $v)
    {
        preg_match('#\\\\([^\\\\]+)Vertex$#', get_class($v), $capt);
        $symbolType = strtolower($capt[1]);
        switch ($symbolType) {
            case 'interface' :
            case 'class' :
            case 'trait' :
                $name = $this->shortenClassname($v->getName());
                break;

            case 'impl' :
            case 'method' :
                list($cls, $meth) = explode('::', $v->getName());
                $name = $this->shortenClassname($cls) . '::' . $meth;
                break;

            case 'param':
                preg_match('#::([^/]+)/(\d+)$#', $v->getName(), $capt);
                $name = $capt[2];
                break;

            default:
                $name = $v->getName();
        }

        $attr = $v->getAttribute();

        return array('name' => $name, 'type' => $symbolType, 'color' => $attr['color']);
    }

    public function export()
    {
        $dump = array();
        $reversed = array();
        foreach ($this->graph->getVertexSet() as $k => $vertex) {
            $reversed[get_class($vertex)][$vertex->getName()] = $k;
            $dump['nodes'][] = $this->exportVertex($vertex);
        }
        foreach ($this->graph->getEdgeSet() as $edge) {
            $v = $edge->getSource();
            $w = $edge->getTarget();
            $dump['links'][] = array(
                'source' => $reversed[get_class($v)][$v->getName()],
                'target' => $reversed[get_class($w)][$w->getName()]
            );
        }

        return json_encode($dump);
    }

}
