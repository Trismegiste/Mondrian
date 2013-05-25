<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Tests\Transform\BuildGraph;

/**
 * MinimalGraphTest tests minimal graph
 */
class MinimalGraphTest extends GraphBuilderTestCase
{

    public function testOneClass()
    {
        $fac = $this->createFactory();
        $stmt = $fac->file('dummy.php')
                ->ns('Project')
                ->declaring($fac->class('One'))
                ->getNode();

        $this->expectsAddVertex(0, 'class', 'Project\One');
        $this->compile(array($stmt));
    }

    public function testOneInterface()
    {
        $fac = $this->createFactory();
        $stmt = $fac->file('dummy.php')
                ->ns('Project')
                ->declaring(new \PHPParser_Node_Stmt_Interface('One'))
                ->getNode();

        $this->expectsAddVertex(0, 'interface', 'Project\One');
        $this->compile(array($stmt));
    }

}