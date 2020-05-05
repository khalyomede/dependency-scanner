# dependency-scanner

Returns a list of packages available for updates.

## Summary

- [About](#about)
- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Examples](#examples)
- [API](#api)
- [Changelog](CHANGELOG.md)

## About

I searched for a package that could provide the same feature some package offer using the command line.

My goal is to display a web page on my web app with a vision of package up to date to help (advanced) users require the administrator help to update packages if needed.

## Features

- Scan for your installed packages in the `composer.lock` file and returns the last available version for it
- Respect your `composer.json` requirements version

## Requirements

- PHP >= 7.0.0
- `composer.lock` alongside your `composer.json` published and ready to be read
- An internet connection (because this library requests Packagist API)

## Installation

In your console, install this package.

```bash
composer require khalyomede/dependency-scanner
```

## Examples

- [1. Getting started](1-getting-started)
- [2. Specify custom file paths](2-specify-custom-file-paths)

### 1. Getting started

In this example, we will use the default configuration to find the file `composer.json` and `composer.lock` and get a list of key pair of out of date packages.

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Khalyomede\DependencyScanner;

$scanner = new DependencyScanner;

$dependencies = $scanner->getOutdatedDependencies();
```

### 2. Specify custom file paths

In this example, we will use custom path (in case your run this script in a certain location where the default file path is not valid anymore).

```php
<?php

use Khalyomede\DependencyScanner;

require __DIR__ . "/../vendor/autoload.php";

$scanner = new DependencyScanner;

$scanner->setComposerFilePath(__DIR__ . "/../composer.json");
$scanner->setLockFilePath(__DIR__ . "/../composer.lock");

$dependencies = $scanner->getOutdatedDependencies();
```

## API

- class `DependencyScanner`
  - [`getComposerFilePath`](#getComposerFilePath)
  - [`getLockFilePath`](#getLockFilePath)
  - [`getOutdatedDependencies`](#getOutdatedDependencies)
  - [`setComposerFilePath`](#setComposerFilePath)
  - [`setLockFilePath`](#setLockFilePath)

### getComposerFilePath

Get the path to the `composer.json` file.

```php
public function getComposerFilePath(): string;
```

### getLockFilePath

Get the path to the `composer.lock` file.

```php
public function getLockFilePath(): string;
```

### getOutdatedDependencies

Get a array of key pairs (name associated with the last available version) of your outdated dependencies.

```php
public function getOutdatedDependencies(): array
```

**throws**

- `Khalyomede\Exception\HttpException`: If we can't connect to the Packagist API.
- `Khalyomede\Exception\JsonDecodeException`: If an error occured while parsing the content of the composer or lock file or while decoding the JSON response of the Packagist API.
- `Khalyomede\Exception\FileNotFoundException`: If the composer or lock file cannot be found in the disk.
- `Khalyomede\Exception\FileNotReadableException`: if the composer or lock file is not readable.
- `Khalyomede\Exception\FileReadFailedException`: If an error occured while reading the composer or lock file (using `file_get_contents()`).

### setComposerFilePath

Set the path to the `composer.json` file.

```php
public function setComposerFilePath(string $path): self;
```

### setLockFilePath

Set the path to the `composer.lock` file.

```php
public function setlockFilePath(string $path): self;
```
