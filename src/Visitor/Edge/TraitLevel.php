<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\Edge;

use PhpParser\Node\Stmt;

/**
 * TraitLevel is ...
 */
class TraitLevel extends ObjectLevelHelper
{

    public function enter(\PhpParser\Node $node)
    {
        
    }

    public function getName()
    {
        return 'trait';
    }

}