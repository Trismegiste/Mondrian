<?php

namespace Project;

class NotConcreteTypedParam implements ContractTypedParam
{

    public function setter(Contract $param)
    {
        echo 42;
    }

}