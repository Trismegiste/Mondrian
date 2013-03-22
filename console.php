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
$application->run();