<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\Edge;

use Trismegiste\Mondrian\Visitor\State\AbstractObjectLevel;
use Trismegiste\Mondrian\Transform\Vertex\ParamVertex;
use PhpParser\Node\Param;

/**
 * ObjectLevelHelper is 
 */
abstract class ObjectLevelHelper extends AbstractObjectLevel
{

    /**
     * Find a class or interface
     *
     * @param string $type fqcn to be found
     * @return Vertex
     */
    protected function findTypeVertex($type)
    {
        foreach (array('class', 'interface') as $pool) {
            $typeVertex = $this->findVertex($pool, $type);
            if (!is_null($typeVertex)) {
                return $typeVertex;
            }
        }

        return null;
    }

    protected function typeHintParam(Param $param, ParamVertex $source)
    {
        if ($param->type instanceof \PhpParser\Node\Name) {
            $paramType = (string) $this->context->getState('file')->resolveClassName($param->type);
            // there is a type, we add a link to the type, if it is found
            $typeVertex = $this->findTypeVertex($paramType);
            if (!is_null($typeVertex)) {
                // we add the edge
                $this->getGraph()->addEdge($source, $typeVertex);
            }
        }
    }

}