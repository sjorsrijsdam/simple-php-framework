<?php

declare(strict_types=1);

namespace App\Users\Presentation;

use App\Users\Infrastructure\UsersRepository;
use Framework\Attributes\AsController;    
use Framework\Attributes\Route;
use Framework\Http\Response;
use Framework\Http\Request;

#[AsController]
class EditController
{
    public function __construct(
        private readonly UsersRepository $usersRepository,
    ) {}

    #[Route(path: '/users/edit', method: 'GET')]
    public function get(Request $request): Response
    {
        $user = $this->usersRepository->getById((int) $request->query['id']);

        $response = <<<HTML
            <html>
                <head>
                    <title>Edit User</title>
                </head>
                <body>
                    <h1>Edit User</h1>
                    <form action="/users/edit" method="post">
                        <input type="hidden" name="id" value="{$user->id}">
                        <p>
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" value="{$user->name}">
                        </p>
                        <p>
                            <label for="email">Email</label>
                            <input type="text" name="email" id="email" value="{$user->email}">
                        </p>
                        <p>
                            <button type="submit">Save</button>
                        </p>
                    </form>
                </body>
            </html>
            HTML;

        return new Response($response);
    }

    #[Route(path: '/users/edit', method: 'POST')]
    public function post(Request $request): Response
    {
        $this->usersRepository->upsert(
            $request->body['name'],
            $request->body['email'],
            (int) $request->body['id']
        );

        return new Response('', 302, ['Location' => '/users']);
    }
}