<?php

declare(strict_types=1);

namespace Framework;

use Framework\Attributes\AsController;
use Framework\Attributes\Route;
use Framework\Http\Request;

class Router
{
    private array $routes = [];

    public function __construct(
        private readonly Container $container,
    ) {
        /** @var \ReflectionClass[] $controllers */
        $controllers = $this->container->getAllByAttribute(AsController::class);

        foreach ($controllers as $controller) {
            $methods = $controller->getMethods(\ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                $attributes = $method->getAttributes(Route::class);

                foreach ($attributes as $attribute) {
                    $route = $attribute->newInstance();

                    $this->routes[strtoupper($route->method) . ' ' . $route->path] = [
                        'controller' => $controller->getName(),
                        'method' => $method->getName(),
                    ];
                }
            }
        }
    }

    public function match(Request $request): ?array
    {
        $key = strtoupper($request->method) . ' ' . $request->path;

        if (!array_key_exists($key, $this->routes)) {
            return null;
        }

        return $this->routes[$key];
    }
}