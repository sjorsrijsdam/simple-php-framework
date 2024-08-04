<?php

declare(strict_types=1);

namespace App\Users\Presentation;

use App\Users\Infrastructure\UsersRepository;
use Framework\Attributes\AsController;    
use Framework\Attributes\Route;
use Framework\Http\Response;
use Framework\Http\Request;

#[AsController]
class DeleteController
{
    public function __construct(
        private readonly UsersRepository $usersRepository,
    ) {}

    #[Route(path: '/users/delete', method: 'POST')]
    public function post(Request $request): Response
    {
        foreach ($request->body['id'] as $id) {
            $this->usersRepository->delete((int) $id);
        }

        return new Response('', 302, ['Location' => '/users']);
    }
}