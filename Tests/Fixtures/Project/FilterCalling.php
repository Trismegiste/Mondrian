<?php

/*
 * To test if same method name well filtered
 */

namespace Project;

interface VeryCommon
{

    function getTitle();
}

class SomeService implements VeryCommon
{

    public function getTitle()
    {
        return 'ze name';
    }

}

class OtherClass
{

    public function getTitle()
    {
        return 'Coincidental nmethod name';
    }

}

class FilterCalling
{

    public function getSpanner()
    {
        return '<span>';
    }

    public function decorate(SomeService $obj)
    {
        $this->getSpanner();  // does not generate an edge
        return $obj->getTitle();  // generates one edge not two
    }

}