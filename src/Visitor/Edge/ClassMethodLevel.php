<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\Edge;

/**
 * ClassMethodLevel is ...
 *
 * @author flo
 */
class ClassMethodLevel extends MethodLevelHelper
{

    public function enter(\PhpParser\Node $node)
    {
        
    }

    public function getName()
    {
        return 'class-method';
    }

}