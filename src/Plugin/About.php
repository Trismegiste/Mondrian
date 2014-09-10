<?php

/*
 * Mondrian
 */

namespace Trismegiste\Mondrian\Plugin;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * About is ...
 *
 * @author flo
 */
class About extends Command
{

    public function configure()
    {
        $this->setName('about')
                ->setDescription("=> You are lost ? Start with this command (^o^)");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $website = 'http://trismegiste.github.io/Mondrian/';
        $command = '$ ./mondrian.phar';
        $message = <<<SIOUX

Ok, you are lost and you don't know what to do ?

Basically, it's a tool to help you to analyse and refactor messy php
source code. It creates graphic views to show you OOP principles
violations. It points out where problems are located, and provides
some simple tools to "inject abstraction" and decouples your classes.
Hence the name: <comment>Mondrian</comment>.

Well, perhaps, start reading the manual of this app here :

     <info>$website</info>

Second, read the workflow :

     <info>{$website}workflow.html</info>

To test it : start with a small set of files, lets say 10 or 20 classes,
and lauch the command

     <info>$command digraph ~/My/Source/Directory</info>

This command will generate a file named 'report.dot' representing
your source code.

You can view this file with 'dot', 'xdot' or other graphviz programs
like a plugin for Netbeans or Eclipse.

     <info>$ dot -Tpng -O report.dot</info> creates a PNG</info>
     <info>$ xdot report.dot</info> launches an interactive view</info>

     Go to http://www.graphviz.org to know more

After that warm-up, you can start analyse the source code with :

     <info>$command spaghetti ~/My/Source/Directory</info>
     <info>$command liskov ~/My/Source/Directory</info>
     <info>$command cycle ~/My/Source/Directory</info>

     etc...

Read the website <info>$website</info> to know more.

SIOUX;

        $output->writeln($message);
    }

}