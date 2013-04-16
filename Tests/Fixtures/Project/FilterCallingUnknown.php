<?php

/*
 * To test if same method name well filtered
 */

namespace Project;

// 3 vertices and 3 edges
class OtherClassFound
{

    public function getTitle()
    {
        return 'Coincidental method name';
    }

}

// 3 vertices and 3 edges
class FilterCallingUnknown
{

    // plus 1 vertex and 2 edges for the parameter (no type found => no edge)
    public function decorate(Unknown $obj)
    {
        // plus 1 edge cause typing is bad 
        // therefore the fallback links with methods of same name.
        return $obj->getTitle();
    }

}