<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\Edge;

/**
 * TraitMethodLevel is a visitor for a method in a trait
 */
class TraitMethodLevel extends MethodLevelHelper
{

    protected function getParentName()
    {
        return 'trait';
    }

    public function getName()
    {
        return 'trait-method';
    }

}