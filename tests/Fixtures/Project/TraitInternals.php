<?php

namespace Project;

trait TraitInternals
{

    public function nonDynCall(Concrete $obj)
    {
        $obj->simple();
    }

    public function staticCall()
    {
        Concrete::simple();
    }

    public function newInstance()
    {
        new OneClass();
    }

}