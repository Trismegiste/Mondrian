<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Builder\Statement;

/**
 * Director is a director for parsing a set of failes
 */
class Director
{

    private $builder;

    public function __construct(BuilderInterface $struct)
    {
        $this->builder = $struct;
    }

    /**
     * Iterator could injected in the builder... or not ?
     */
    public function parse(\Iterator $iter)
    {
        $this->builder->buildLexer();
        $this->builder->buildFileLevel();
        $this->builder->buildPackageLevel();

        return $this->builder->getParsed($iter);
    }

}