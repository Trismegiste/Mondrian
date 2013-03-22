<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Fixtures\Mess;

use Trismegiste\Mondrian\Tests\Fixtures\Bad;

class Highly4 extends Highly1
{

    public function getOne()
    {
        $this->inner->accessor(null, 2);
    }

    public function getTwo()
    {
        $this->inner->accessor(null, 2);
    }

}