<?php

declare(strict_types=1);

namespace App\Domain\Document;

interface DocumentRepository
{
    /**
     * @return Document[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return array
     * @throws DocumentNotFoundException
     */
    public function findDocumentOfId(int $id): array;

    /**
     * Find documents for a given section id
     *
     * @param int $sectionId
     * @return Document[]
     */
    public function findBySection(int $sectionId): array;

    /**
     * Add a document record and return info (e.g. inserted id)
     * @param array $document
     * @return array
     */
    public function add(array $document): array;
}
