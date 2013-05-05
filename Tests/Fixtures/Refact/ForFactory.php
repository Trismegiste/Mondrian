<?php

namespace Refact;

class ForFactory
{

    public function service()
    {
        $obj = new Earth();
        $obj->getName();
        $obj->attract(new Moon());
    }

}