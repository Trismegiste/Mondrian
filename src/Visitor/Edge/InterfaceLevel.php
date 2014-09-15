<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Visitor\Edge;

use PhpParser\Node\Stmt;

/**
 * InterfaceLevel is ...
 */
class InterfaceLevel extends ObjectLevelHelper
{

    public function enter(\PhpParser\Node $node)
    {
        
    }

    public function getName()
    {
        return 'interface';
    }

}