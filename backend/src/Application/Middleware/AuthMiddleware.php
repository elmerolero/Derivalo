<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware implements Middleware
{
    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        try {
            // Check the auth token
            $authHeader = $request -> getHeaderLine('Authorization');
            if ($authHeader === '' || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $match)) {
                $response = new \Slim\Psr7\Response();

                $response->getBody() -> write(
                    json_encode(['error' => 'Unauthorized'])
                );

                return $response->withStatus(401);
            }

            $token = $match[1];
            $key = new Key($_ENV['JWT_SECRET'], 'HS256');
            $payload = JWT::decode($token, $key);
            $request = $request->withAttribute('user', (int)$payload -> sub);

            return $handler->handle($request);
        }
        catch(\Throwable $e) {
            $response = new \Slim\Psr7\Response();
            $response -> getBody() -> write(json_encode(['error' => 'Unauthorized']));
            return $response->withStatus(401);
        }   
    }
}
