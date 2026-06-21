<?php
declare(strict_types=1);

namespace App\Domain\Document;

use JsonSerializable;

class Document implements JsonSerializable
{
    private ?int $pkDocument;

    private string $name;

    private string $description;

    private ?int $fkSection;

    private ?string $file;

    private bool $available;

    public function __construct( ?int $pk_document, string $name, string $description, ?int $fk_section, bool $available, ?string $file = null )
    {
        $this->pkDocument = $pk_document;
        $this->name = $name;
        $this->description = $description;
        $this->fkSection = $fk_section;
        $this->available = $available;
        $this->file = $file;
    }

    public function setPkDocument(int $pkDocument): void
    {
        $this->pkDocument = $pkDocument;
    }

    public function getPkDocument(): ?int
    {
        return $this->pkDocument;
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
    
    public function getFkSection(): ?int
    {
        return $this->fkSection;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'pk_document' => $this->pkDocument,
            'name' => $this->name,
            'description' => $this->description,
            'fk_section' => $this->fkSection,
            'available' => $this->available,
            'file' => $this->file
        ];
    }
}
