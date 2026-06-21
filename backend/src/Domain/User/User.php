<?php

declare(strict_types=1);

namespace App\Domain\User;

use JsonSerializable;
use DateTime;

class User implements JsonSerializable
{
    private ?int $id;

    private string $email;

    private string $password;

    private DateTime $creationDate;

    private DateTime $lastUpdateDate;

    public function __construct(?int $id, string $email, string $password, DateTime $creationDate, DateTime $lastUpdateDate)
    {
        $this -> id = $id;
        $this -> email = strtolower($email);
        $this -> password = $password;
        $this -> creationDate = $creationDate;
        $this -> lastUpdateDate = $lastUpdateDate;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this -> email;
    }

    public function getPassword(): string
    {
        return $this -> password;
    }

    public function setPassword(string $password): void
    {
        $this -> password = $password;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'email' => $this -> email,
            'creationDate' => $this -> creationDate,
            'lastUpdateDate' => $this -> lastUpdateDate
        ];
    }
}
