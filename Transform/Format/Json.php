<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Transform\Format;

use Trismegiste\Mondrian\Graph\Vertex;

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
        $result = array();
        foreach ($ns as $item) {
            $result[] = $item[0];
        }
        array_push($result, $short);

        return implode('\\', $result);
    }

    private function exportVertex(Vertex $v)
    {
        preg_match('#\\\\([^\\\\]+)$#', get_class($v), $capt);
        switch ($capt[1]) {
            case 'InterfaceVertex' :
            case 'ClassVertex' :
                $name = $this->shortenClassname($v->getName());
                break;

            case 'ImplVertex' :
            case 'MethodVertex' :
                list($cls, $meth) = explode('::', $v->getName());
                $name = $this->shortenClassname($cls) . '::' . $meth;
                if ($capt[1][0] == 'I')
                    $name = "[$name]";
                break;

            case 'ParamVertex':
                preg_match('#::([^/]+)/(\d+)$#', $v->getName(), $capt);
                $name = $capt[1] . '/' . $capt[2];
                break;

            default:
                $name = $v->getName();
        }

        return array('name' => $name);
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
                'target' => $reversed[get_class($w)][$w->getName()],
                'value' => 1
            );
        }

        return json_encode($dump);
    }

}