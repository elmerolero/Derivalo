<?php

declare(strict_types=1);

use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\User\DbUserRepository;
use App\Domain\Visitor\VisitorRepository;
use App\Infrastructure\Persistence\Visitor\InMemoryVisitorRepository;
use App\Domain\Section\SectionRepository;
use App\Infrastructure\Persistence\Section\DatabaseSectionRepository;
use App\Domain\Document\DocumentRepository;
use App\Infrastructure\Persistence\Document\DatabaseDocumentRepository;
use App\Domain\RefreshToken\RefreshTokenRepository;
use App\Infrastructure\Persistence\RefreshToken\DatabaseRefreshTokenRepository;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our UserRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([
        UserRepository::class => \DI\autowire(DbUserRepository::class),
        VisitorRepository::class => \DI\autowire(InMemoryVisitorRepository::class),
        SectionRepository::class => \DI\autowire(DatabaseSectionRepository::class),
        DocumentRepository::class => \DI\autowire(DatabaseDocumentRepository::class)
        ,
        RefreshTokenRepository::class => \DI\autowire(DatabaseRefreshTokenRepository::class)
    ]);
};
