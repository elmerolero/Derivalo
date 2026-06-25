<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Actions\User\UserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;

class UserRefreshAction extends UserAction
{
    protected function action(): Response
    {
        // Read refresh token from cookie
        $raw = $_COOKIE['refresh_token'] ?? null;
        if (!$raw) {
            return $this->respondWithData(['error' => 'No refresh token'], 401);
        }

        try {
            $hash = hash('sha256', $raw);
            $refresh = $this->refreshTokenRepository->findByTokenHash($hash);
        }
        catch (\Throwable $e) {
            return $this->respondWithData(['error' => 'Invalid refresh token'], 401);
        }

        // Check revoked and expiration
        if ($refresh -> isRevoked() || $refresh -> expiresAt() < new \DateTime()) {
            return $this->respondWithData(['error' => 'Refresh token expired or revoked'], 401);
        }

        // Issue new access token
        $accessToken = JWT::encode([
            'sub' => $refresh -> fkUser(),
            'iat' => time(),
            'exp' => time() + 900
        ], $_ENV['JWT_SECRET'], 'HS256');


        // Rotate refresh token: revoke old, add new
        $newRefreshToken = bin2hex(random_bytes(32));
        $hash = hash('sha256', $newRefreshToken);

        $this -> refreshTokenRepository->add(
            $refresh -> fkUser(),
            $hash,
            new \DateTime('+30 days'),
            false
        );

        $this->refreshTokenRepository->revokeById($refresh->pkRefreshToken());

        // Set cookie for client (path restricted to this endpoint)
        setcookie(
            'refresh_token',
            $newRefreshToken,
            [
                'expires' => time() + 60 * 60 * 24 * 30,
                'path' => '/api/auth/refresh',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'None'
            ]
        );

        return $this->respondWithData(['access_token' => $accessToken]);
    }
}
