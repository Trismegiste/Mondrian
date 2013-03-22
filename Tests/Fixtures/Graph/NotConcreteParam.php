<?php

namespace Trismegiste\Mondrian\Tests\Fixtures\Graph;

class NotConcreteParam implements ContractParam
{

    public function setter($param)
    {
        echo 42;
    }

}