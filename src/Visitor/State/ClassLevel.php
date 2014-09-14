<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\State;

use PhpParser\Node;

/**
 * ClassLevel is ...
 */
class ClassLevel extends AbstractState
{

    public function enter(Node $node)
    {
        switch ($node->getType()) {

            case 'Stmt_TraitUse' :
                $this->importSignatureTrait($node);
                break;

            case 'Stmt_ClassMethod':
                if ($node->type === Node\Stmt\Class_::MODIFIER_PUBLIC) {
                    $classNode = $this->context->getNodeFor('class');
                    $fileState = $this->context->getState('file');
                    $fqcn = $fileState->getNamespacedName($classNode);
                    $this->context->getReflectionContext()->addMethodToClass($fqcn, $node->name);
                }
                break;
        }
    }

    protected function importSignatureTrait(Node\Stmt\TraitUse $node)
    {
        $classNode = $this->context->getNodeFor('class');
        $fileState = $this->context->getState('file');
        $fqcn = $fileState->getNamespacedName($classNode);
        // @todo do not forget aliases
        foreach ($node->traits as $import) {
            $name = (string) $stateFile->resolveClassName($import);
            $this->context->getReflectionContext()->initSymbol($name, ReflectionContext::SYMBOL_TRAIT);
            $this->context->getReflectionContext()->pushUseTrait($fqcn, $name);
        }
    }

    public function getName()
    {
        return 'class';
    }

}