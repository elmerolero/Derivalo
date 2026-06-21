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