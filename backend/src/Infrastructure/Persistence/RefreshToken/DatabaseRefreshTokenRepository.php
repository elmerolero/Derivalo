<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\RefreshToken;

use App\Domain\RefreshToken\RefreshToken;
use App\Domain\RefreshToken\RefreshTokenRepository;
use InvalidArgumentException;
use DI\NotFoundException;
use DateTime;
use PDO;

class DatabaseRefreshTokenRepository implements RefreshTokenRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function add(int $fkUser, string $tokenHash, DateTime $expiresAt, bool $revoked): int
    {
        $query = "INSERT INTO sc_refresh_token (fk_user, token_hash, expires_at, revoked, created_at)\n
                  VALUES (:fk_user, :token_hash, :expires_at, :revoked, :created_at)";

        $stmt = $this -> db -> prepare($query);

        $stmt->execute([
            ':fk_user' => $fkUser,
            ':token_hash' => $tokenHash,
            ':expires_at' => $expiresAt -> format('Y-m-d H:i:s'),
            ':revoked' => $revoked,
            ':created_at' => (new DateTime()) ->format('Y-m-d H:i:s')
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function findByTokenHash(string $tokenHash): RefreshToken
    {
        $sql = "SELECT * FROM sc_refresh_token WHERE token_hash = :token_hash";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':token_hash', $tokenHash, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            throw new NotFoundException('Refresh token not found');
        }

        return new RefreshToken(
            (int)$result['pk_refresh_token'],
            (int)$result['fk_user'],
            $result['token_hash'],
            new DateTime($result['expires_at']),
            (bool)$result['revoked'],
            new DateTime($result['created_at'])
        );
    }

    public function findByRawToken(string $rawToken): RefreshToken
    {
        $sql = "SELECT * FROM sc_refresh_token WHERE revoked = 0 AND expires_at > NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $result) {
            if (password_verify($rawToken, $result['token_hash'])) {
                return new RefreshToken(
                    (int)$result['pk_refresh_token'],
                    (int)$result['fk_user'],
                    $result['token_hash'],
                    new DateTime($result['expires_at']),
                    (bool)$result['revoked'],
                    new DateTime($result['created_at'])
                );
            }
        }

        throw new NotFoundException('Refresh token not found');
    }

    public function revokeById(int $id): void
    {
        $sql = "UPDATE sc_refresh_token SET revoked = 1 WHERE pk_refresh_token = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    public function revokeByUserId(int $userId): void
    {
        $sql = "UPDATE sc_refresh_token SET revoked = 1 WHERE fk_user = :userId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':userId' => $userId]);
    }

    public function deleteExpired(): void
    {
        $sql = "DELETE FROM sc_refresh_token WHERE expires_at < NOW()";
        $this->db->exec($sql);
    }
}
