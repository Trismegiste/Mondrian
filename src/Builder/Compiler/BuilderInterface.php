<?php

namespace Trismegiste\Mondrian\Builder\Compiler;

use PhpParser\NodeVisitor;

/**
 * BuilderInterface is a contract to build a compiler
 */
interface BuilderInterface
{

    public function buildContext();

    public function buildCollectors();

    public function buildTraverser(NodeVisitor $collector);
}