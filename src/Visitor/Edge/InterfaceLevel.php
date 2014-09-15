<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\Edge;

use PhpParser\Node\Stmt;

/**
 * InterfaceLevel is ...
 */
class InterfaceLevel extends ObjectLevelHelper
{

    public function enter(\PhpParser\Node $node)
    {
        if ($node->getType() == 'Stmt_ClassMethod') {
            // NS
            $methodName = $node->name;
            $currentFqcn = $this->getCurrentFqcn();
            $declaringFqcn = $this->getReflectionContext()->getDeclaringClass($currentFqcn, $methodName);
            // Vertices
            $signatureIndex = $declaringFqcn . '::' . $methodName;
            $classVertex = $this->findVertex('interface', $currentFqcn);
            $signatureVertex = $this->findVertex('method', $signatureIndex);

            // if current class == declaring class, we add the edge
            if ($declaringFqcn == $currentFqcn) {
                $this->getGraph()->addEdge($classVertex, $signatureVertex); // I -> M
                // and we link the signature to the params
                foreach ($node->params as $idx => $param) {
                    // adding edge from signature to param :
                    $paramVertex = $this->findVertex('param', $signatureIndex . '/' . $idx);
                    // it is possible to not find the param because the signature
                    // is external to the source code :
                    if (!is_null($paramVertex)) {
                        $this->getGraph()->addEdge($signatureVertex, $paramVertex); // M -> P
                        // now the type of the param :
                        $this->typeHintParam($param, $paramVertex);
                    }
                }
            }
        }
    }

    public function getName()
    {
        return 'interface';
    }

}