<?php

namespace Khalyomede;

class Dependency
{
    private $name;
    private $version;
    private $pattern;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->version = "";
        $this->pattern = "";
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function setPattern(string $pattern): self
    {
        $this->pattern = $pattern;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }
}
