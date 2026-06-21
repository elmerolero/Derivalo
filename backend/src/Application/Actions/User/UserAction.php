<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Domain\User\UserRepository;
use App\Domain\RefreshToken\RefreshTokenRepository;
use Psr\Log\LoggerInterface;

abstract class UserAction extends Action
{
    protected UserRepository $userRepository;
    protected RefreshTokenRepository $refreshTokenRepository;

    public function __construct(LoggerInterface $logger, RefreshTokenRepository $refreshTokenRepository, UserRepository $userRepository)
    {
        parent::__construct($logger);
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->userRepository = $userRepository;
    }
}
