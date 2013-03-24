<?php

namespace Project;

class NewInstance
{

    public function simpleNew()
    {
        $obj = new Concrete();
    }

    public function dynamicNew()
    {
        $cls = 'Concrete';
        $obj = new $cls();
    }

    protected function notTracked()
    {
        $obj = new Concrete();
    }

}