<?php

/*
 *  with Moon, two poorly coded classes
 */

namespace Refact;

// @mondrian contractor EarthInterface
class Earth
{

    public function getName()
    {
        return 'Earth';
    }

    public function attract(Moon $m)
    {
        return "Fly me to the " . $m->getName();
    }

}
