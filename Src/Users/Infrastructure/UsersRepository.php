<?php

declare(strict_types=1);

namespace App\Users\Infrastructure;

use App\Users\Domain\User;

class UsersRepository
{
    private ?\SQLite3 $db = null;

    public function __construct(
        private readonly string $usersDbPath,
    ) {}

    /** @return User[] */
    public function getAll(): array
    {
        $users = [];
        $result = $this->getInstance()->query('SELECT * FROM users');

        while ($row = $result->fetchArray()) {
            $users[] = new User($row['id'], $row['name'], $row['email']);
        }

        return $users;
    }
    
    public function getById(int $id): User
    {
        $stmt = $this->getInstance()->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->bindValue(':id', $id, \SQLITE3_INTEGER);
        $result = $stmt->execute();
        $row = $result->fetchArray();

        return new User($row['id'], $row['name'], $row['email']);
    }

    public function upsert(string $name, string $email, int $id = null): void
    {
        if (!$id) {
            $id = random_int(1, \PHP_INT_MAX);
        }

        $stmt = $this->getInstance()->prepare('REPLACE INTO users (id, name, email) VALUES (:id, :name, :email)');
        $stmt->bindValue(':id', $id, \SQLITE3_INTEGER);
        $stmt->bindValue(':name', $name, \SQLITE3_TEXT);
        $stmt->bindValue(':email', $email, \SQLITE3_TEXT);
        $stmt->execute();
    }

    /** @param int[] $ids */
    public function delete(int $id): void
    {
        $stmt = $this->getInstance()->prepare('DELETE FROM users WHERE id = :id');
        $stmt->bindValue(':id', $id, \SQLITE3_INTEGER);
        $stmt->execute();
    }

    private function getInstance(): \SQLite3
    {
        if (!$this->db) {
            $this->db = new \SQLite3($this->usersDbPath);

            $this->db->exec('CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, name TEXT, email TEXT)');
        }

        return $this->db;
    }
}