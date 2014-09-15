<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\State;

/**
 * AbstractObjectLevel is a helper for class/trait/interface level state
 */
abstract class AbstractObjectLevel extends AbstractState
{

    /**
     * returns the current fqcn of this class|trait|interface
     * 
     * @return string
     */
    protected function getCurrentFqcn()
    {
        $objectNode = $this->context->getNodeFor($this->getName());
        $fileState = $this->context->getState('file');
        $fqcn = $fileState->getNamespacedName($objectNode);

        return $fqcn;
    }

}