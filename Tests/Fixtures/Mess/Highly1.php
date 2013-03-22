<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Fixtures\Mess;

use Trismegiste\Mondrian\Tests\Fixtures\Bad;

class Highly1
{

    protected $inner;

    public function __construct(Bad $obj)
    {
        $this->inner = $obj;
    }
    
    protected function getThing()
    {
        $this->inner->accessor(null, 1);
        $this->inner->otherMethod();
    }

}