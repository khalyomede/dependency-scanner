<?php

use Khalyomede\DependencyScanner;

require __DIR__ . "/../vendor/autoload.php";

$scanner = new DependencyScanner;

$scanner->setComposerFilePath(__DIR__ . "/../composer.json");
$scanner->setLockFilePath(__DIR__ . "/../composer.lock");

$dependencies = $scanner->getOutdatedDependencies();

print_r($dependencies);
