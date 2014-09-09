#!/usr/bin/env php
<?php

/*
 * Standalone Console
 *
 * Uses the Symfony Console Component (which is great !)
 */

require_once __DIR__ . '/vendor/autoload.php';

use Trismegiste\Mondrian\Plugin\Application;
use Trismegiste\Mondrian\Command;

$info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'composer.json'));

/*
 * Init application
 */
$application = new Application('Mondrian', '1.2');
$application->addCommands(array(
    new Command\TypeHintConfig(),
    new Command\DigraphCommand(),
    new Command\UsedCentralityCommand(),
    new Command\DependCentralityCommand(),
    new Command\HiddenCouplingCommand(),
    new Command\SpaghettiCommand(),
    new Command\CyclicCommand(),
    new Command\LiskovCommand(),
    new Command\ContractorCommand(),
    new Command\BadInterfaceCommand(),
    new Command\FactoryGenerator()
));

/*
 * init plugins
 */
$application->addPlugin($info->extra->plugins);
$application->run();
