<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Refactor;

use Trismegiste\Mondrian\Visitor;

/**
 * FactoryGenerator refactors a package by extracting all
 * new instances and puts it in protected methods.
 */
class FactoryGenerator extends AbstractRefactoring
{

    protected function buildRefactoringPass()
    {
        return array(new Visitor\NewInstanceRefactor($this->phpDumper));
    }

}