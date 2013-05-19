<?php

namespace Refact;

class ForFactory
{

    public function service()
    {
        $obj = new Earth();
        $obj->getName();
        $obj->attract(new Moon());
        $withVar = new Astroid($name);
        $withParam = new Planet('Earth');
        $complexParam = new Comet($param['period']);
    }

}
