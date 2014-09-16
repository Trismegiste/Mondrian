<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\Edge;

use PhpParser\Node;

/**
 * TraitLevel is ...
 */
class TraitLevel extends TraitUserHelper
{

    public function enter(Node $node)
    {
        switch ($node->getType()) {

            case 'Stmt_ClassMethod':
                if ($node->isPublic()) {
                    $this->context->pushState('trait-method', $node);
                    $this->enterPublicMethod($node);
                }
                break;

            case 'Stmt_TraitUse':
                $fqcn = $this->getCurrentFqcn();
                $currentVertex = $this->findVertex('trait', $fqcn);
                $this->enterTraitUse($node, $currentVertex);
                break;
        }
    }

    public function getName()
    {
        return 'trait';
    }

    protected function enterPublicMethod(Node\Stmt\ClassMethod $node)
    {
        // NS
        $methodName = $node->name;
        $currentFqcn = $this->getCurrentFqcn();
        // Vertices
        $traitVertex = $this->findVertex('trait', $currentFqcn);
        $implVertex = $this->findVertex('impl', $currentFqcn . '::' . $methodName);
        // edge between impl and trait :
        $this->getGraph()->addEdge($implVertex, $traitVertex);
        $this->getGraph()->addEdge($traitVertex, $implVertex);

        // edges between impl towards param (with typed param)
        foreach ($node->params as $idx => $param) {
            // adding edge from implementation to param :
            $paramVertex = $this->findVertex('param', "$currentFqcn::$methodName/$idx");
            $this->getGraph()->addEdge($implVertex, $paramVertex);
            // now the type of the param :
            $this->typeHintParam($param, $paramVertex);
        }

        // edge between class vertex which using the trait and copy-pasted methods :
        $traitUser = $this->getReflectionContext()->getClassesUsingTraitForDeclaringMethod($currentFqcn, $methodName);
        foreach ($traitUser as $classname) {
            // we link the class and the signature
            $source = $this->findVertex('class', $classname);
            $target = $this->findVertex('method', $classname . '::' . $methodName);
            $this->getGraph()->addEdge($source, $target);
            // and we link the copypasted signature to unique parameter
            foreach ($node->params as $idx => $param) {
                $paramVertex = $this->findVertex('param', "$currentFqcn::$methodName/$idx");
                $this->getGraph()->addEdge($target, $paramVertex);
            }
        }
    }

}