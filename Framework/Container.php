<?php

declare(strict_types=1);

namespace Framework;

class Container
{
    /** @var \ReflectionClass[] */
    private array $reflections = [];
    private array $instances = [];
    private array $bind = [];

    public function __construct()
    {
        $this->instances[self::class] = $this;
    }

    /**
     * @param string[] $services
     * @param string[] $bind
     */
    public function load(array $services, array $bind): void
    {
        $this->bind = array_merge($this->bind, $bind);

        foreach ($services as $namespaceRoot => $fileRoot) {
            $realRoot = realpath($fileRoot);

            $files = $this->walkDir($realRoot);

            $this->loadFiles($files);
            
            $classes = $this->getClassPaths($files, $namespaceRoot, $realRoot);

            $this->store($classes);
        }
    }

    /** @return \ReflectionClass[] */
    public function getAllByAttribute(string $targetAttribute): array
    {
        return array_filter($this->reflections, function (\ReflectionClass $reflection) use ($targetAttribute) {
            $attributes = $reflection->getAttributes();

            foreach ($attributes as $attribute) {
                if ($attribute->getName() === $targetAttribute) {
                    return true;
                }
            }
        });
    }

    public function get(string $class): mixed
    {
        if (!array_key_exists($class, $this->instances)) {
            $reflection = $this->reflections[$class];
            $constructor = $reflection->getConstructor();

            if ($constructor) {
                $constructorParams = $constructor->getParameters();
                $params = array_map(function (\ReflectionParameter $param) {
                    $paramName = $param->getName();
                    $paramClassName = $param->getType();
    
                    if (array_key_exists('$' . $paramName, $this->bind)) {
                        return $this->bind['$' . $paramName];
                    }

                    return $this->get($paramClassName->getName());
                }, $constructorParams);
            }
            
            $this->instances[$class] = $reflection->newInstance(...$params ?? []);
        }

        return $this->instances[$class];
    }

    /** @return string[] */
    private function walkDir(string $realRoot): array
    {
        $files = [];

        $iterator = new \DirectoryIterator($realRoot);
        foreach ($iterator as $file) {
            if ($file->isDot()) {
                continue;
            }

            if ($file->getType() === 'file' && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }

            if ($file->getType() === 'dir') {
                $files = array_merge($files, $this->walkDir($file->getPathname()));
            }
        }

        return $files;
    }

    /** @param string[] $files */
    private function loadFiles(array $files): void
    {
        foreach ($files as $file) {
            require_once $file;
        }
    }

    /** @param string[] $files */
    private function getClassPaths(array $files, string $namespaceRoot, string $fileRoot): array
    {
        return array_map(function (string $filePath)  use ($namespaceRoot, $fileRoot) {
            $strippedExtension = str_replace('.php', '', $filePath);
            $replacedRoot = str_replace($fileRoot, $namespaceRoot, $strippedExtension);

            return str_replace(DIRECTORY_SEPARATOR, '\\', $replacedRoot);
        }, $files);
    }

    /** @param string[] $classes */
    private function store(array $classes): void
    {
        foreach ($classes as $class) {
            $this->reflections[$class] = new \ReflectionClass($class);
        }
    }
}