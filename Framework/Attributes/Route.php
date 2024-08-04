<?php

declare(strict_types=1);

namespace Framework\Attributes;

#[\Attribute]
class Route
{
    public function __construct (
        public readonly string $path,
        public readonly string $method,
    ) {}
}