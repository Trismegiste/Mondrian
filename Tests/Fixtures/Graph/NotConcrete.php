<?php

namespace Trismegiste\Mondrian\Tests\Fixtures\Graph;

class NotConcrete implements Contract
{

    public function simple()
    {
        echo 42;
    }

}