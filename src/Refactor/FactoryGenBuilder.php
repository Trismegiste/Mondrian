<?php

/*
 * MondrianCubox
 */

namespace Trismegiste\Mondrian\Refactor;

use Trismegiste\Mondrian\Visitor;

/**
 * FactoryGenBuilder builds the compiler for the factory generator 
 * refactoring service
 */
class FactoryGenBuilder extends RefactoringBuilder
{

    public function buildCollectors()
    {
        return array(new Visitor\NewInstanceRefactor($this->dumper));
    }

    public function buildContext()
    {
        
    }

}