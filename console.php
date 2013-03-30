<?php

/*
 * Standalone Console
 */

namespace Trismegiste\Mondrian;

require_once 'vendor/autoload.php';

use Trismegiste\Mondrian\Command;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new Command\DigraphCommand());
$application->add(new Command\MetricsCommand());
$application->add(new Command\CentralityCommand());
$application->add(new Command\HiddenCouplingCommand());
$application->run();