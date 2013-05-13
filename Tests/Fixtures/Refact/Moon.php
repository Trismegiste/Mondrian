<?php

namespace Refact;

// @mondrian contractor MoonInterface
class Moon
{

    public function getName()
    {
        return 'Moon';
    }

    public function orbiting(Earth $m)
    {
        return "Circling around the " . $m->getName();
    }

}
