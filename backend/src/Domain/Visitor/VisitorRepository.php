<?php

declare(strict_types=1);

namespace App\Domain\Visitor;

interface VisitorRepository
{
    /**
     * @return Visitor[]
     */
    public function findAll(): array;

    /**
     * @return Visitor
     */
    public function add(string $userAgent, string $ip, string $timestamp): Visitor;
}
