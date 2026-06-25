<?php

declare(strict_types=1);

namespace App\Application\Actions\User;
use App\Application\Actions\User\UserAction;
use Psr\Http\Message\ResponseInterface as Response;

class UserLogoutAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        // Invalidate refresh token
        $raw = $_COOKIE['refresh_token'] ?? null;
        try {
            if (!$raw);
            else{
                $hash = hash('sha256', $raw);
                $refresh = $this->refreshTokenRepository->findByTokenHash($hash);
                $this->refreshTokenRepository->revokeById($refresh->pkRefreshToken());
            }
        }
        catch (\Throwable $e) {
            // Nothing to do here
        }

        setcookie(
            'refresh_token',
            '',
            [
                'expires' => time() - 60 * 60 * 24 * 30,
                'path' => '/api/auth/refresh',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'None'
            ]
        );

        return $this->respondWithData([
            'data' => 'Log out successful'
        ]);
    }
}