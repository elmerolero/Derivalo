<?php

declare(strict_types=1);

namespace App\Domain\User;

use InvalidArgumentException;

interface UserRepository
{
    /**
     * @return User[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return User
     * @throws UserNotFoundException
     */
    public function findUserOfId(int $id): User;

    /**
     * @param string email
     * @return User
     * @throws UserNotFoundException
     */
    public function findUserOfEmail(string $email): User;

    /**
     * @param array $input
     * @return int
     * @throws InvalidArgumentException
     */
    public function add(array $input): int;
}
