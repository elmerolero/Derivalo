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
            $userId = 0;
            $authHeader = $this -> request -> getHeaderLine('Authorization');
            if ($authHeader === '' || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $match)) {
                return $this -> respondWithData([
                        'pk_user' => 0,
                        'email' => ''
                    ], 
                    401
                );
            }
            $token = $match[1];
            $decoded = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key($_ENV['JWT_SECRET'], 'HS256'));
            $userId = isset($decoded->sub) ? (int)$decoded -> sub : 0;
            if ($userId === 0) {
                // return anonymous (frontend expects 0 when not logged)
                return $this -> respondWithData([
                    'pk_user' => 0,
                    'email' => ''], 
                    401
                );
            }
            $user = $this->userRepository->findUserOfId((int)$userId);

            // return minimal user info and csrf token
            return $this->respondWithData([
                'pk_user' => $user -> getId(),
                'email' => $user -> getEmail()
            ]);
        }
        catch (\Throwable $e) {
            return $this -> respondWithData([
                'pk_user' => 0,
                'email' => ''], 
                401
            );
        }
    }
}