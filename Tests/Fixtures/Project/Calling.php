<?php

namespace Project;

class Calling
{

    public function simpleCall()
    {
        $obj->simple(42);
    }

    public function dynamicCall()
    {
        $m = 'calling';
        $obj->$m(42);
    }

    protected function notTracked()
    {
        $obj->simple(42);
    }

}