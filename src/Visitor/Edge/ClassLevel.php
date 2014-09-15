<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\Edge;

use PhpParser\Node\Stmt;

/**
 * ClassLevel is ...
 */
class ClassLevel extends TraitUserHelper
{

    public function enter(\PhpParser\Node $node)
    {
        switch ($node->getType()) {

            case 'Stmt_ClassMethod':
                if ($node->isPublic()) {
                    $this->context->pushState('class-method', $node);
                    $this->enterPublicMethod($node);
                }
                break;

            case 'Stmt_TraitUse':
                $fqcn = $this->getCurrentFqcn();
                $currentVertex = $this->findVertex('class', $fqcn);
                $this->enterTraitUse($node, $currentVertex);
                break;
        }
    }

    public function getName()
    {
        return 'class';
    }

    protected function enterPublicMethod(Stmt\ClassMethod $node)
    {
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
                    // now the type of the param :
                    $this->typeHintParam($param, $paramVertex);
                }
            }
        }
    }

}