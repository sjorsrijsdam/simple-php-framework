<?php

declare(strict_types=1);

namespace Framework\Http;

class Request
{
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly array $query = [],
        public readonly array $body = [],
    ) {}

    public static function createFromGlobals(): self
    {
        return new self(
            $_SERVER['REQUEST_METHOD'],
            $_SERVER['PATH_INFO'] ?? '/',
            $_GET,
            $_POST,
        );
    }
}