<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Builder\Compiler;

/**
 * Design pattern : Builder
 *
 * Director makes a compilation/generation/transform with a compiler
 */
class Director
{

    private $builder;

    /**
     * The builder contains the components to create for making the compilation
     *
     * @param \Trismegiste\Mondrian\Builder\Compiler\BuilderInterface $structure
     */
    public function __construct(BuilderInterface $structure)
    {
        $this->builder = $structure;
    }

    /**
     * Does the compilation of the parsed statements
     *
     * @param array $stmts
     */
    public function compile(array $stmts)
    {
        $this->builder->buildContext();
        $pass = $this->builder->buildCollectors();

        foreach ($pass as $collector) {
            $traverser = $this->builder->buildTraverser($collector);
            $traverser->traverse($stmts);
        }
    }

}