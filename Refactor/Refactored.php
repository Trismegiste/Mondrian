<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Refactor;

/**
 * Refactored is a container for refactoring changes
 */
class Refactored
{

    protected $newContract = array();

    /**
     * Stack a new contract for a concrete class
     *
     * @param string $fqcn FQCN
     * @param string $interfaceName name of interface (fully qualified)
     */
    public function pushNewContract($fqcn, $interfaceName)
    {
        if (in_array($interfaceName, $this->newContract)) {
            throw new \LogicException("Two classes want to create the same contract");
        }
        $this->newContract[$fqcn] = $interfaceName;
    }

    /**
     * Is there a new contract for a concrete class ?
     *
     * @param string $fqcn FQCN
     */
    public function hasNewContract($fqcn)
    {
        return array_key_exists($fqcn, $this->newContract);
    }

    /**
     * Get the new contract for a concrete class
     *
     * @param string $fqcn FQCN
     */
    public function getNewContract($fqcn)
    {
        return $this->newContract[$fqcn];
    }

}
