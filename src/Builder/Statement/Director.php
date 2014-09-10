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
     * Parses a list of php files
     * 
     * @param \Iterator $iter an iterator of SplFileInfo
     * @return array an array of PHPParser_Node
     */
    public function parse(\Iterator $iter)
    {
        $this->builder->buildLexer();
        $this->builder->buildFileLevel();
        $this->builder->buildPackageLevel();

        return $this->builder->getParsed($iter);
    }

}