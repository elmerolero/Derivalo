<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Visitor;

use App\Domain\Visitor\Visitor;
use App\Domain\Visitor\VisitorRepository;

class InMemoryVisitorRepository implements VisitorRepository
{
    /**
     * @var Visitor[]
     */
    private array $visitors;
    private static int $count;

    /**
     * @param Visitor[]|null $visitors
     */
    public function __construct(array $visitors = null)
    {
        $this->visitors = $visitors ?? [];
        InMemoryVisitorRepository::$count = count($this->visitors) + 1;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        return array_values($this->visitors);
    }

    public function add(string $userAgent, string $ip, string $timestamp): Visitor
    {
        $id = InMemoryVisitorRepository::$count++;
        $visitor = new Visitor($id, $userAgent, $ip, $timestamp);
        array_push($this->visitors, $visitor);
        return $visitor;
    }
}
