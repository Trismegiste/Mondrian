<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\State;

/**
 * ObjectLevel is a helper for class/trait/interface level state
 */
abstract class ObjectLevel extends AbstractState
{

    protected function getCurrentFqcn()
    {
        $objectNode = $this->context->getNodeFor($this->getName());
        $fileState = $this->context->getState('file');
        $fqcn = $fileState->getNamespacedName($objectNode);

        return $fqcn;
    }

}