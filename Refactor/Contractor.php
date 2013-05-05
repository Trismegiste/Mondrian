<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Refactor;

use Trismegiste\Mondrian\Visitor;

/**
 * Contractor refactors a list of classes with annotations hints.
 * 
 * What it does ?
 *  * It creates a new interface for each class with annotation 
 *    like "@mondrian contractor NewInterfaceName".
 *  * it replaces all these classes by their new interface in 
 *    methods parameters (public or not, this is important)
 *  * it adds the inheritance for NewInterfaceName
 * 
 * Each interface is stored in the same namespace, neighbour of the
 * class in a directory. NewInterfaceName is a short name not a FQCN.
 * It is not possible to store the generated content in another directory 
 * since everybody uses Git or at least SVN. Therefore you can launch the
 * test suite immediatly.
 * 
 */
class Contractor extends AbstractRefactoring
{

    /**
     * Build the refactoring passes with context
     * 
     * @return FqcnHelper[]
     */
    protected function buildRefactoringPass()
    {
        $context = new Refactored();

        $pass = array();
        // finds which class must be refactored (and add inheritance)
        $pass[0] = new Visitor\NewContractCollector($context);
        // replaces the parameters types with the interface
        $pass[1] = new Visitor\ParamRefactor($context);
        // creates the new interface file
        $pass[2] = new Visitor\InterfaceExtractor($context, $this->phpDumper);

        return $pass;
    }

}