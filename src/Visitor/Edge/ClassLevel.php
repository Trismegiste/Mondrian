<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\Edge;

use PhpParser\Node\Stmt;
use Trismegiste\Mondrian\Transform\Vertex\MethodVertex;

/**
 * ClassLevel is ...
 */
class ClassLevel extends ObjectLevelHelper
{

    public function enter(\PhpParser\Node $node)
    {
        switch ($node->getType()) {

            case 'Stmt_ClassMethod':
                if ($node->isPublic()) {
//                    $this->context->pushState('class-method', $node);
                    $this->enterPublicMethod($node);
                }
                break;

            case 'Stmt_TraitUse':
                break;
        }
    }

    public function getName()
    {
        return 'class';
    }

    protected function enterPublicMethod(Stmt\ClassMethod $node)
    {
        $fileState = $this->context->getState('file');
        // NS
        $methodName = $node->name;
        $currentFqcn = $this->getCurrentFqcn();
        $declaringFqcn = $this->getReflectionContext()->getDeclaringClass($currentFqcn, $methodName);
        // Vertices
        $signatureIndex = $declaringFqcn . '::' . $methodName;
        $classVertex = $this->findVertex('class', $currentFqcn);
        $signatureVertex = $this->findVertex('method', $signatureIndex);
        $implVertex = $this->findVertex('impl', $currentFqcn . '::' . $methodName);

        // if current class == declaring class, we add the edge
        if ($declaringFqcn == $currentFqcn) {
            $this->getGraph()->addEdge($classVertex, $signatureVertex); // C -> M
            if (!$node->isAbstract()) {
                $this->getGraph()->addEdge($signatureVertex, $implVertex); // M -> S
                $this->getGraph()->addEdge($implVertex, $classVertex); // S -> C
            }
        } else {
            if (!$node->isAbstract()) {
                $this->getGraph()->addEdge($classVertex, $implVertex); // C -> S
                $this->getGraph()->addEdge($implVertex, $classVertex); // S -> C
            }
        }

        // in any case, we link the implementation to the params
        foreach ($node->params as $idx => $param) {
            // adding edge from signature to param :
            $paramVertex = $this->findVertex('param', $signatureIndex . '/' . $idx);
            // it is possible to not find the param because the signature
            // is external to the source code :
            if (!is_null($paramVertex)) {
                if (!$node->isAbstract()) {
                    $this->getGraph()->addEdge($implVertex, $paramVertex); // S -> P
                }
                if ($currentFqcn === $declaringFqcn) {
                    $this->getGraph()->addEdge($signatureVertex, $paramVertex); // M -> P
                }
                // now the type of the param :
                if ($param->type instanceof \PhpParser\Node\Name) {
                    $paramType = (string) $fileState->resolveClassName($param->type);
                    // there is a type, we add a link to the type, if it is found
                    $typeVertex = $this->findTypeVertex($paramType);
                    if (!is_null($typeVertex)) {
                        // we add the edge
                        $this->getGraph()->addEdge($paramVertex, $typeVertex);
                    }
                }
            }
        }
    }

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

}