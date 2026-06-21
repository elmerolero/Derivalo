<?php

declare(strict_types=1);

namespace App\Domain\Visitor;

use JsonSerializable;

class Visitor implements JsonSerializable
{
    private ?int $id;

    private string $userAgent;

    private string $ip;

    private string $timestamp;

    public function __construct(?int $id, string $userAgent, string $ip, string $timestamp)
    {
        $this->id = $id;
        $this->userAgent = $userAgent;
        $this->ip = $ip;
        $this->timestamp = $timestamp;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserAgent(): string
    {
        return $this->username;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'userAgent' => $this->userAgent,
            'ip' => $this->ip,
            'timestamp' => $this->timestamp,
        ];
    }
}
