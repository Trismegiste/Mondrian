<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Utils;

/**
 * ReflectionTree is n extension to ReflectionClass for tracking method 
 * declaration in a tree of inheritance
 */
class ReflectionTree extends \ReflectionClass
{

    public function findFirstDeclaration($method)
    {
        return $this->recursivDeclaration($this, $method);
    }

    protected function recursivDeclaration(\ReflectionClass $current, $method)
    {
        if ($current->hasMethod($method)) {
            // default declarer :
            $src = $current->getMethod($method);
            $declarer = $src->getDeclaringClass();

            // stacking parents :
            $parent = array();
            if ($declarer->getParentClass()) {
                $parent[] = $declarer->getParentClass();
            }
            foreach ($declarer->getInterfaces() as $contract) {
                $parent[] = $contract;
            }

            // higher parent ?
            foreach ($parent as $mother) {
                $tmp = $this->recursivDeclaration($mother, $method);
                if ($tmp) {
                    $declarer = $tmp;
                    break;
                }
            }

            return $declarer;
        }

        return null;
    }

}