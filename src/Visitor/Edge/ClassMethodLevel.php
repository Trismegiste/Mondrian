<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\Edge;

/**
 * ClassMethodLevel is a visitor for method in a class
 */
class ClassMethodLevel extends MethodLevelHelper
{

    public function getName()
    {
        return 'class-method';
    }

    protected function getParentName()
    {
        return 'class';
    }

}