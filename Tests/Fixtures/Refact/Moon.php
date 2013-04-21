<?php

namespace Trismegiste\Mondrian\Tests\Fixtures\Refact;

// @mondrian contractor MoonInterface
class Moon
{

    public function orbiting(Earth $m)
    {
        echo "I don't spin but I'm rotating";
    }

}