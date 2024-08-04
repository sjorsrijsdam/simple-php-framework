<?php

declare(strict_types=1);

namespace Framework;

use Framework\Attributes\AsController;
use Framework\Http\Request;
use Framework\Http\Response;

class App
{
    public function __construct(
        private readonly Container $container,
        private readonly Router $router,
    ) {
    }

    public function run(): void
    {
        $request = Request::createFromGlobals();
        $route = $this->router->match($request);
        $response = null;

        if ($route) {
            $controller = $this->container->get($route['controller']);
            $method = $route['method'];

            $response = $controller->$method($request);
        }

        if (!$response) {
            $response = new Response('Not Found', 404);
        }

        $response->send();
    }
}