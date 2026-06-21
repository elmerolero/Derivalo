<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Document;

use App\Domain\Document\Document;
use App\Domain\Document\DocumentRepository;
use PDO;

class DatabaseDocumentRepository implements DocumentRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findAll(): array
    {
        $stmt = $this->db->prepare('SELECT * FROM ct_documents');
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function($row){
            return new Document(
                (int)$row['pk_document'],
                $row['name'],
                $row['description'],
                isset($row['fk_section']) ? (int)$row['fk_section'] : null,
                (bool)$row['available'],
                isset($row['file']) ? $row['file'] : null
            );
        }, $rows);
    }

    public function findDocumentOfName(string $name): Document
    {
        $stmt = $this->db->prepare('SELECT * FROM ct_documents WHERE name = :name');
        $stmt->execute([':name' => $name]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            throw new \Exception('Document not found');
        }

        return new Document(
            (int)$row['pk_document'],
            $row['name'],
            $row['description'],
            isset($row['fk_section']) ? (int)$row['fk_section'] : null,
            (bool)$row['available'],
            isset($row['file']) ? $row['file'] : null
        );
    }

    public function add(array $input): array
    {
        $query = "INSERT INTO ct_documents (name, description, fk_section, available, file) VALUES (:name, :description, :fk_section, :available, :file)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':name' => $input['name'],
            ':description' => $input['description'],
            ':fk_section' => $input['fk_section'],
            ':available' => $input['available']
            ,':file' => $input['file']
        ]);

        $pk = $this->db->lastInsertId();
        return ['pk_document' => $pk, 'name' => $input['name'], 'file' => $input['file']];
    }

    public function findBySection(int $sectionId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM ct_documents WHERE fk_section = :fk_section');
        $stmt->execute([':fk_section' => $sectionId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function($row){
            return new Document(
                (int)$row['pk_document'],
                $row['name'],
                $row['description'],
                isset($row['fk_section']) ? (int)$row['fk_section'] : null,
                (bool)$row['available'],
                isset($row['file']) ? $row['file'] : null
            );
        }, $rows);
    }
}
