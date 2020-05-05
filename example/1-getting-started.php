<?php

require __DIR__ . '/../vendor/autoload.php';

use Khalyomede\DependencyScanner;

$scanner = new DependencyScanner;

$dependencies = $scanner->getOutdatedDependencies();

print_r($dependencies);
