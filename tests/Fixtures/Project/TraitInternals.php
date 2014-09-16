<?php

namespace Project;

trait TraitInternals
{

    public function nonDynCall(TraitConfig $obj)
    {
        $obj->calling();
    }

    public function staticCall()
    {
        TraitHelper::simple();
    }

    public function newInstance()
    {
        new TraitDocument();
    }

}

class TraitConfig {
    public function calling() {}
}

class TraitHelper {
    static public function simple() {}
}

class TraitDocument {}