<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Section;
use App\Domain\Section\Section;
use App\Domain\Section\SectionRepository;
use PDO;

class DatabaseSectionRepository implements SectionRepository
{
    private PDO $db;
    
    /**
    * @param PDO $db
    */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        $stmt = $this->db->prepare("select * from ct_sections");
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, Section::class);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($row) {
            return new Section(
                (int)$row['pk_section'],
                $row['name'],
                $row['description'],
                $row['path'],
                (int)$row['fk_parent'],
                (bool)$row['available']
            );
        }, $rows);
    }

    /**
     * {@inheritdoc}
     */
    public function add(array $input): Section
    {
        // Gerates the section
        $section = new Section(
            $input['pk_section'],
            $input['name'],
            $input['description'],
            $input['path'],
            $input['fk_parent'],
            $input['available']
        );

        $query = "INSERT INTO ct_sections (name, description, path, fk_parent, available)
                  VALUES (:name, :description, :path, :fk_parent, :available)";

        $stmt = $this->db->prepare($query);
        
        $stmt->execute([
            ':name' => $section->getName(),
            ':description' => $section->getDescription(),
            ':path' => $section->getPath(),
            ':fk_parent' => $section->getFkParent(),
            ':available' => $section->isAvailable()
        ]);

        // Get generated ID
        $pkSection = $this->db->lastInsertId();
        $section->setPkSection((int)$pkSection);
        return $section;
    }
}
