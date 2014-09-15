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

    public function testHintType()
    {
        $fac = $this->createFactory();
        $pack[0] = $fac->file('param.php')
                ->ns('Project')
                ->declaring($fac
                        ->class('Cfg'))
                ->getNode();
        $pack[1] = $fac->file('service.php')
                ->ns('Project')
                ->declaring($fac
                        ->class('Service')
                        ->addStmt($fac
                                ->method('run')
                                ->addParam($fac
                                        ->param('obj')
                                        ->setTypeHint('Cfg'))))
                ->getNode();

        $this->expectsAddEdge(10, 'param', 'Project\Service::run/0', 'class', 'Project\Cfg');
        $this->expectsAddEdge(8, 'impl', 'Project\Service::run', 'param', 'Project\Service::run/0');
        $this->compile($pack);
    }

}