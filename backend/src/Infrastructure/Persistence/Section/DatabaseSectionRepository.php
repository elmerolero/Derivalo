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
        // Generate the section
        $section = new Section(
            $input['pk_section'] ?? null,
            $input['name'],
            $input['description'],
            $input['fk_parent'] ?? null,
            $input['available'] ?? 1
        );

        $query = "INSERT INTO ct_sections (name, description, fk_parent, available)
                  VALUES (:name, :description, :fk_parent, :available)";

        $stmt = $this->db->prepare($query);

        $stmt->execute([
            ':name' => $section->getName(),
            ':description' => $section->getDescription(),
            ':fk_parent' => $section->getFkParent(),
            ':available' => $section->isAvailable()
        ]);

        // Get generated ID
        $pkSection = $this->db->lastInsertId();
        $section->setPkSection((int)$pkSection);
        return $section;
    }

    /**
     * Update an existing section
     */
    public function update(array $input): Section
    {
        $query = "UPDATE ct_sections SET name = :name, description = :description, fk_parent = :fk_parent, available = :available WHERE pk_section = :pk_section";
        $stmt = $this->db->prepare($query);

        $stmt->execute([
            ':name' => $input['name'],
            ':description' => $input['description'],
            ':fk_parent' => $input['fk_parent'],
            ':available' => $input['available'],
            ':pk_section' => $input['pk_section']
        ]);

        return new Section(
            (int)$input['pk_section'],
            $input['name'],
            $input['description'],
            (int)$input['fk_parent'],
            (bool)$input['available']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function findSectionOfName(string $name): Section
    {
        $sql = "SELECT pk_section, name, description, fk_parent, available
                FROM ct_sections
                WHERE name = :name";

        $stmt = $this -> db -> prepare($sql);

        $stmt -> bindValue(':name', $name, PDO::PARAM_STR);

        $stmt -> execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$result)
            return null;

        return new Section(
            (int)$result['pk_section'],
            $result['name'],
            $result['description'],
            (int)$result['fk_parent'],
            (bool)$result['available']
        );
    }
}
