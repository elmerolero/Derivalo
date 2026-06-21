<?php

declare(strict_types=1);

namespace App\Domain\RefreshToken;

use DateTime;

class RefreshToken
{
    private int $pkRefreshToken;
    private int $fkUser;
    private string $tokenHash;
    private DateTime $expiresAt;
    private bool $revoked;
    private DateTime $createdAt;

    public function __construct(
        int $pkRefreshToken,
        int $fkUser,
        string $tokenHash,
        DateTime $expiresAt,
        bool $revoked,
        DateTime $createdAt
    ) {
        $this->pkRefreshToken = $pkRefreshToken;
        $this->fkUser = $fkUser;
        $this->tokenHash = $tokenHash;
        $this->expiresAt = $expiresAt;
        $this->revoked = $revoked;
        $this->createdAt = $createdAt;
    }

    public function pkRefreshToken(): int
    {
        return $this->pkRefreshToken;
    }

    public function fkUser(): int
    {
        return $this->fkUser;
    }

    public function tokenHash(): string
    {
        return $this->tokenHash;
    }

    public function expiresAt(): DateTime
    {
        return $this->expiresAt;
    }

    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    public function createdAt(): DateTime
    {
        return $this->createdAt;
    }
}
