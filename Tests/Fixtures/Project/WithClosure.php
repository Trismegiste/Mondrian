<?php

namespace Trismegiste\Mondrian\Tests\Fixtures\Project;

class WithClosure
{

    public function sample()
    {
        $lambda = function ($param) {
                    echo $param;
                };

        $lambda('coucou');
    }

}