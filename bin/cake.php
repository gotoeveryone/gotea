#!/usr/bin/php -q
<?php
// Check platform requirements
require dirname(__DIR__) . '/config/requirements.php';
require dirname(__DIR__) . '/vendor/autoload.php';

use Cake\Console\CommandRunner;
use Gotea\Application;

// Build the runner with an application and root executable name.
$runner = new CommandRunner(new Application(dirname(__DIR__) . '/config'), 'cake');
exit($runner->run($argv));
