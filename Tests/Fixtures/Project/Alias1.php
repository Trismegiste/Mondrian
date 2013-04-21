<?php

/*
 * For testing alias
 */

namespace Project;

use Project\Maid as Marion;
use Project\Peril as Premonition;

class Aliasing extends Marion implements Premonition
{

    public function spokes(Premonition $obj)
    {
        
    }

}