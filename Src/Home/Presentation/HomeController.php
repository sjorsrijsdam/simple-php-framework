<?php

declare(strict_types=1);

namespace App\Home\Presentation;

use Framework\Attributes\AsController;    
use Framework\Attributes\Route;
use Framework\Http\Response;

#[AsController]
class HomeController
{
    #[Route(path: '/', method: 'GET')]
    public function get(): Response
    {
        $responseBody = <<<HTML
            <html>
                <head>
                    <title>Hello, World!</title>
                </head>
                <body>
                    <h1>Hello, World!</h1>
                    <p>
                        <a href="/users">Users</a>
                    </p>
                </body>
            </html>
            HTML;

        return new Response($responseBody);
    }
}