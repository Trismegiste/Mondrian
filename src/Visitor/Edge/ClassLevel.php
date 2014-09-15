<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\Edge;

use PhpParser\Node\Stmt;

/**
 * ClassLevel is ...
 */
class ClassLevel extends ObjectLevelHelper
{

    public function enter(\PhpParser\Node $node)
    {
        
    }

    public function getName()
    {
        return 'class';
    }

}