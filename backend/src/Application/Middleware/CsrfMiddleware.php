<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class CsrfMiddleware implements Middleware
{
    /**
     * {@inheritdoc}
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $method = strtoupper($request->getMethod());
        // Only validate on state-changing requests
        if (in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }

            $sessionToken = $_SESSION['csrf_token'] ?? null;
            $headerToken = $request->getHeaderLine('X-CSRF-Token');

            if (!$sessionToken || !$headerToken || !hash_equals($sessionToken, $headerToken)) {
                $response = new \Slim\Psr7\Response();
                $response->getBody()->write(json_encode(['error' => 'CSRF validation failed']));
                return $response->withStatus(403);
            }
        }

        return $handler->handle($request);
    }
}
