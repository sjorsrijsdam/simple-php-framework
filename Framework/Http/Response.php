<?php

declare(strict_types=1);

namespace Framework\Http;

class Response
{
    public function __construct(
        public readonly string $content,
        public readonly int $status = 200,
        public readonly array $headers = [],
    ) {}

    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        echo $this->content;
    }
}