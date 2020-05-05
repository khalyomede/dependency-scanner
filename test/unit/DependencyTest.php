<?php

use Khalyomede\Dependency;
use PHPUnit\Framework\TestCase;

final class DependencyTest extends TestCase
{
    public function testShouldSetTheName(): void
    {
        $dependencyName = "foo";
        $expected = $dependencyName;
        $actual = (new Dependency($dependencyName))->getName();

        $this->assertEquals($actual, $expected);
    }

    public function testShouldSetTheVersion(): void
    {
        $version = "0.1.2";
        $dependency = new Dependency("foo");
        $dependency->setVersion($version);
        $expected = $version;
        $actual = $dependency->getVersion();

        $this->assertEquals($actual, $expected);
    }

    public function testShouldSetThePattern(): void
    {
        $pattern = "^1.0.0";
        $dependency = new Dependency("foo");
        $dependency->setPattern($pattern);
        $expected = $pattern;
        $actual = $dependency->getPattern();

        $this->assertEquals($actual, $expected);
    }
}
