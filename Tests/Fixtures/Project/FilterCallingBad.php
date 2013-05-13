<?php

/*
 * To test if same method name well filtered
 */

namespace Project;

// 1 vertex
interface VeryCommonContract
{

}

// 3 vertices and 4 edges
class SomeServiceBad implements VeryCommonContract
{

    function getTitle()
    {
        return 'aaa';
    }

}

// 3 vertices and 3 edges
class OtherClass
{

    public function getTitle()
    {
        return 'Coincidental nethod name';
    }

}

// 3 vertices and 3 edges
class FilterCallingBad
{

    // plus 1 vertex and 3 edges for the parameter
    public function decorate(VeryCommonContract $obj)
    {
        // generates two edges cause typing is bad
        // therefore the fallback links with methods of same name.
        // getTitle is not in the interface but in a subclass (Yes PHP can do that)
        // by the way it should generates 2 hidden coupling
        return $obj->getTitle();
    }

}
