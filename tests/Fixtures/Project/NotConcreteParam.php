<?php

namespace Project;

class NotConcreteParam implements ContractParam
{

    public function setter($param)
    {
        echo 42;
    }

}
