<?php

declare(strict_types=1);

namespace App\Domain\RefreshToken;
use InvalidArgumentException;
use DateTime;

interface RefreshTokenRepository
{
    /**
     * @param array $input Must contain keys: fk_user, token_hash, expires_at, revoked, created_at
     * @return int Inserted primary key
     * @throws InvalidArgumentException
     */
    public function add(int $fkUser, string $tokenHash, DateTime $expiresAt, bool $revoked): int;

    /**
     * @param string $tokenHash
     * @return RefreshToken
     */
    public function findByTokenHash(string $tokenHash): RefreshToken;

    /**
     * Find a refresh token by the raw token value (verifies hashes).
     */
    public function findByRawToken(string $rawToken): RefreshToken;

    /**
     * Revoke a refresh token by its primary key
     */
    public function revokeById(int $id): void;

    /**
     * Revoke all refresh tokens for a given user
     */
    public function revokeByUserId(int $userId): void;

    /**
     * Delete expired tokens
     */
    public function deleteExpired(): void;
}
