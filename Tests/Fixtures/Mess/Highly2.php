<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Fixtures\Mess;

use Trismegiste\Mondrian\Tests\Fixtures\Bad;

class Highly2
{

    public function coupled(Bad $obj)
    {
        $obj->accessor(new \stdClass(), 1);
    }
    
    public function notRelated($obj)
    {
        
    }

}