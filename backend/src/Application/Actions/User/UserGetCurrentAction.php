<?php

declare(strict_types=1);

namespace App\Application\Actions\User;
use App\Application\Actions\User\UserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use InvalidArgumentException;

class UserGetCurrentAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        try {
            // Try to get user id from Bearer token
            $userId = $this -> request -> getAttribute('user');
            if(!isset($userId) || $userId === 0){
                return $this -> respondWithData([ 'pk_user' => 0, 'email' => ''], 401);
            }

            // Gets user data
            $user = $this->userRepository -> findUserOfId((int)$userId);

            // return minimal user info and csrf token
            return $this->respondWithData([
                'pk_user' => $user -> getId(),
                'email' => $user -> getEmail()
            ]);
        }
        catch (\Throwable $e) {
            return $this -> respondWithData([ 'pk_user' => 0, 'email' => ''], 401);
        }
    }
}