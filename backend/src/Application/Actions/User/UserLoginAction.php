<?php

declare(strict_types=1);

namespace App\Application\Actions\User;
use App\Application\Actions\User\UserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;
use DateTime;
use DateInterval;

class UserLoginAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $data = $this->request->getParsedBody();
        $user = $this -> validateCrentials($data);
        if($user === false) {
            $this -> response -> getBody() -> write(json_encode(['error' => 'Invalid credentials']));
            return $this -> response -> withStatus(400);
        }
        
        // Generating a new token
        $accessToken = JWT::encode([
            'sub' => $user -> getId(),
            'iat' => time(),
            'exp' => time() + 900 // 15 min
        ], $_ENV['JWT_SECRET'], 'HS256');

        $refreshToken = bin2hex(random_bytes(32));
        $hash = hash('sha256', $refreshToken);
        
        $this -> refreshTokenRepository -> add(
            $user -> getId(),
            $hash,
            new \DateTime('+30 days'),
            false
        );

        setcookie(
            'refresh_token',
            $refreshToken,
            [
                'expires' => time() + 60 * 60 * 24 * 30,
                'path' => '/api/auth',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'None'
            ]
        );

        // Set header
        return $this->respondWithData([
            'access_token' => $accessToken
        ]);
    }

    private function validateCrentials(array $data) {
        try {
            $email = isset($data['email']) ? filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL) : false;
            if (!$email) {
                return false;
            }

            $password = isset($data['password']) ? trim($data['password']) : '';
            if ($password === '') {
                return false;
            }

            $user = $this -> userRepository -> findUserOfEmail($email);
            if (!password_verify($password, $user->getPassword())) {
                return false;
            }
    
            return $user;
        }
        catch (\Throwable $e) {
            return false;
        }
    }
}