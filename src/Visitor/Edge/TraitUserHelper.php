<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\Edge;

use PhpParser\Node\Stmt;
use Trismegiste\Mondrian\Graph\Vertex;

/**
 * TraitUserHelper is an helper for trait users (class or trait)
 */
abstract class TraitUserHelper extends ObjectLevelHelper
{

    /**
     * Adds an edge from a class|trait toward a trait
     * @param \PhpParser\Node\Stmt\TraitUse $node
     * @param \Trismegiste\Mondrian\Graph\Vertex $source
     */
    protected function enterTraitUse(Stmt\TraitUse $node, Vertex $source)
    {
        $fileState = $this->context->getState('file');
        foreach ($node->traits as $import) {
            $name = (string) $fileState->resolveClassName($import);
            $target = $this->findVertex('trait', $name);
            // it's possible to not find a trait if it coming from an external library for example
            // or could be dead code too
            if (!is_null($target)) {
                $this->getGraph()->addEdge($source, $target);
            }
        }
    }

}