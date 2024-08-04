<?php

declare(strict_types=1);

namespace App\Users\Presentation;

use App\Users\Infrastructure\UsersRepository;
use Framework\Attributes\AsController;    
use Framework\Attributes\Route;
use Framework\Http\Response;
use Framework\Http\Request;

#[AsController]
class CreateController
{
    public function __construct(
        private readonly UsersRepository $usersRepository,
    ) {}

    #[Route(path: '/users/create', method: 'GET')]
    public function get(): Response
    {
        $response = <<<HTML
            <html>
                <head>
                    <title>Create User</title>
                </head>
                <body>
                    <h1>Create User</h1>
                    <form action="/users/create" method="post">
                        <p>
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name">
                        </p>
                        <p>
                            <label for="email">Email</label>
                            <input type="text" name="email" id="email">
                        </p>
                        <p>
                            <button type="submit">Create</button>
                        </p>
                    </form>
                </body>
            </html>
            HTML;

        return new Response($response);
    }

    #[Route(path: '/users/create', method: 'POST')]
    public function post(Request $request): Response
    {
        $this->usersRepository->upsert($request->body['name'], $request->body['email']);

        return new Response('', 302, ['Location' => '/users']);
    }
}