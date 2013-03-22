<?php

namespace Trismegiste\Mondrian\Tests\Fixtures\Graph;

class NotConcreteTypedParam implements ContractTypedParam
{

    public function setter(Contract $param)
    {
        echo 42;
    }

}