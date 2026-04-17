<?php
declare(strict_types=1);

namespace App\Domain\Section;

use JsonSerializable;

class Section implements JsonSerializable
{
    private ?int $pkSection;

    private string $name;

    private string $description;
    
    private string $path;

    private ?int $fkParent;

    private bool $available;

    public function __construct( ?int $pk_section, string $name, string $description, string $path, ?int $fk_parent, bool $available )
    {
        $this->pkSection = $pk_section;
        $this->name = $name;
        $this->description = $description;
        $this->path = $path;
        $this->fkParent = $fk_parent;
        $this->available = $available;
    }

    public function setPkSection(int $pkSection): void
    {
        $this->pkSection = $pkSection;
    }

    public function getPkSection(): ?int
    {
        return $this->pkSection;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
    
    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPath(): string
    {
        return $this->path;
    }
    
    public function getFkParent(): ?int
    {
        return $this->fkParent;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'pk_section' => $this->pkSection,
            'name' => $this->name,
            'description' => $this->description,
            'fk_parent' => $this->fkParent,
            'path' => $this->path,
            'available' => $this->available
        ];
    }
}
