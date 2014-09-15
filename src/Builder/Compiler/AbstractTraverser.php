<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Builder\Compiler;

use PhpParser\NodeVisitor;
use PhpParser\NodeTraverser;

/**
 * AbstractTraverser partly builds the compiler with a traverser
 */
abstract class AbstractTraverser implements BuilderInterface
{

    public function buildTraverser(NodeVisitor $collector)
    {
        $traverser = new NodeTraverser();
        $traverser->addVisitor($collector);

        return $traverser;
    }

}