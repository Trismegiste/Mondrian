<?php

/*
 *  with Moon, two poorly coded classes
 */

namespace Refact;

// @mondrian contractor EarthInterface
class Earth
{

    public function attract(Moon $m)
    {
        echo "I'm center of everything (from my referential)";
    }

}