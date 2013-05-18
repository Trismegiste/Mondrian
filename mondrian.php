<?php

/*
 * Standalone Console
 *
 * Uses the Symfony Console Component (which is great !)
 */

namespace Trismegiste\Mondrian;

require_once 'vendor/autoload.php';

use Trismegiste\Mondrian\Command;
use Symfony\Component\Console\Application;

$info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'composer.json'));
// application
$application = new Application('Mondrian', $info->version);
$application->addCommands(array(
    new Command\DigraphCommand(),
    new Command\UsedCentralityCommand(),
    new Command\DependCentralityCommand(),
    new Command\HiddenCouplingCommand(),
    new Command\SpaghettiCommand(),
    new Command\CyclicCommand(),
    new Command\LiskovCommand(),
    new Command\ContractorCommand(),
    new Command\BadInterfaceCommand()
));
$application->run();
