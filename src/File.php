<?php

namespace Khalyomede;

use Khalyomede\Exception\FileNotFoundException;
use Khalyomede\Exception\FileReadFailedException;
use Khalyomede\Exception\FileNotReadableException;
use Khalyomede\Exception\JsonDecodeException;

class File
{
    private $path;
    private $contentString;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->contentString = "";
    }

    public function toJson(): array
    {
        $this->setContent();

        return $this->getJson();
    }

    private function setContent(): void
    {
        if (!file_exists($this->path)) {
            throw new FileNotFoundException("file not found: {$this->path}");
        }

        if (!is_readable($this->path)) {
            throw new FileNotReadableException("file not readable: {$this->path}");
        }

        $content = file_get_contents($this->path);

        if ($content === false) {
            throw new FileReadFailedException("unable to get content of file {$this->path}");
        }

        $this->contentString = $content;
    }

    private function getJson(): array
    {
        $json = json_decode($this->contentString, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonDecodeException(json_last_error_msg());
        }

        return $json;
    }
}
