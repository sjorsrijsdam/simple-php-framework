<?php

declare(strict_types=1);

namespace App\Users\Presentation;

use App\Users\Infrastructure\UsersRepository;
use Framework\Attributes\AsController;    
use Framework\Attributes\Route;
use Framework\Http\Response;

#[AsController]
class OverviewController
{
    public function __construct(
        private readonly UsersRepository $usersRepository,
    ) {}

    #[Route(path: '/users', method: 'GET')]
    public function get(): Response
    {
        $users = $this->usersRepository->getAll();

        $responseBody = $this->buildResponseBody($users);
        return new Response($responseBody);
    }

    private function buildResponseBody(array $users): string
    {
        $tableBody = '';

        foreach ($users as $user) {
            $tableBody .= <<<HTML
                <tr>
                    <td>
                        <input type="checkbox" name="id[]" value="{$user->id}">
                    </td>
                    <td>{$user->name}</td>
                    <td>{$user->email}</td>
                    <td>
                        <a href="/users/edit?id={$user->id}">Edit</a>
                    </td>
                </tr>
            HTML;
        }

        return <<<HTML
            <html>
                <head>
                    <title>Users Overview</title>
                </head>
                <body>
                    <h1>Users Overview</h1>
                    <form action="users/delete" method="post">
                        <table>
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                {$tableBody}
                            </tbody>
                        </table>
                        <button type="submit">Delete selected</button>
                    </form>
                    <p>
                        <a href="/users/create">Create User</a>
                    </p>
                </body>
            </html>
        HTML;
    }
}