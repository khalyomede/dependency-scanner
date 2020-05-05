<?php

namespace Khalyomede;

use GuzzleHttp\Client;
use Composer\Semver\Semver;
use Composer\Semver\Comparator;
use Khalyomede\Exception\HttpException;
use Khalyomede\Exception\JsonDecodeException;

class DependencyScanner
{
    const API_BASE_URI = "https://repo.packagist.org/";
    const DEFAULT_COMPOSER_FILE_PATH = "./composer.json";
    const DEFAULT_LOCK_FILE_PATH = "./composer.lock";

    private $composerFilePath;
    private $lockFilePath;

    public function __construct()
    {
        $this->composerFilePath = static::DEFAULT_COMPOSER_FILE_PATH;
        $this->lockFilePath = static::DEFAULT_LOCK_FILE_PATH;
    }

    public function setLockFilePath(string $path): self
    {
        $this->lockFilePath = $path;

        return $this;
    }

    public function setComposerFilePath(string $path): self
    {
        $this->composerFilePath = $path;

        return $this;
    }

    public function getLockFilePath(): string
    {
        return $this->lockFilePath;
    }

    public function getComposerFilePath(): string
    {
        return $this->composerFilePath;
    }

    public function getOutdatedDependencies(): array
    {
        $dependencies = $this->getDependencies();
        $outdatedVersions = [];

        foreach ($dependencies as $dependency) {
            $localVersion = $dependency->getVersion();
            $name = $dependency->getName();
            $pattern = $dependency->getPattern();

            $versions = $this->getDependencyVersions($name);

            $greaterVersions = array_filter($versions, function ($version) use ($localVersion, $pattern) {
                $version = $version->getVersion();

                return Semver::satisfies($version, $pattern) && Comparator::greaterThan($version, $localVersion);
            });

            usort($greaterVersions, function ($dependencyA, $dependencyB) {
                $versionA = $dependencyA->getVersion();
                $versionB = $dependencyB->getVersion();
                if (Comparator::equalTo($versionA, $versionB)) {
                    return 0;
                }

                return Comparator::greaterThan($versionA, $versionB) ? 1 : -1;
            });

            $greaterVersions = array_values($greaterVersions);

            if (count($greaterVersions) > 0) {
                $outdatedVersions[$name] = end($greaterVersions)->getVersion();
            }
        }

        return $outdatedVersions;
    }

    private function getDependencies(): array
    {
        $lockDependencies = $this->getLockDependencies();
        $composerDependencies = $this->getComposerDependencies();
        $dependencies = [];

        foreach ($lockDependencies as $lockDependency) {
            foreach ($composerDependencies as $composerDependency) {
                if ($lockDependency->getName() === $composerDependency->getName()) {
                    $dependency = new Dependency($lockDependency->getName());
                    $dependency->setVersion($lockDependency->getVersion());
                    $dependency->setPattern($composerDependency->getPattern());

                    $dependencies[] = $dependency;

                    break;
                }
            }
        }

        return $dependencies;
    }

    private function getLockDependencies(): array
    {
        $json = (new File($this->lockFilePath))->toJson();

        $packages = $json["packages"] ?? [];

        $packages = array_map(function ($package) {
            $dependency = new Dependency($package["name"]);
            $dependency->setVersion($package["version"]);

            return $dependency;
        }, $packages);

        return $packages;
    }

    private function getComposerDependencies(): array
    {
        $json = (new File($this->composerFilePath))->toJson();
        $packages = $json["require"] ?? [];
        $dependencies = array_map(function ($name, $pattern) {
            $dependency = new Dependency($name);
            $dependency->setPattern($pattern);

            return $dependency;
        }, array_keys($packages), array_values($packages));

        return $dependencies;
    }

    private function getDependencyVersions(string $dependencyName): array
    {
        $request = new Client([
            "base_uri" => static::API_BASE_URI,
        ]);
        $url = "/packages/$dependencyName.json";

        $response = $request->get($url);

        if ($response->getStatusCode() !== 200) {
            throw new HttpException("calling $url resulted in a non 200 status code");
        }

        $response = json_decode($response->getBody()->getContents(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonDecodeException(json_last_error_msg());
        }

        $versions = array_filter($response["package"]["versions"], function ($package) {
            return array_key_exists("version", $package);
        });

        $versions = array_values(array_map(function ($version) {
            $dependency = new Dependency($version["name"]);
            $dependency->setVersion($version["version"]);

            return $dependency;
        }, $versions));

        return $versions;
    }
}
