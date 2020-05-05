<?php

use Khalyomede\DependencyScanner;
use PHPUnit\Framework\TestCase;

final class DependencyScannerTest extends TestCase
{
    public function testShouldReturnNewVersionsIfOutOfDates(): void
    {
        $scanner = new DependencyScanner;
        $scanner->setComposerFilePath(__DIR__ . "/../sample/composer.json");
        $scanner->setLockFilePath(__DIR__ . "/../sample/composer.lock");
        $expected = [
            "guzzlehttp/guzzle" => "6.4.1"
        ];
        $actual = $scanner->getOutdatedDependencies();

        $this->assertEquals($actual, $expected);
    }

    public function testShouldNotReturnVersionsIfNotOutOfDates(): void
    {
        $scanner = new DependencyScanner;
        $scanner->setComposerFilePath(__DIR__ . "/../sample/composer2.json");
        $scanner->setLockFilePath(__DIR__ . "/../sample/composer2.lock");
        $expected = [];
        $actual = $scanner->getOutdatedDependencies();

        $this->assertEquals($actual, $expected);
    }
}
