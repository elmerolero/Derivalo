<?php

declare(strict_types=1);

namespace App\Domain\Section;

interface SectionRepository
{
    /**
     * @return Section[]
     */
    public function findAll(): array;

    /**
     * @param string $name
     * @return Section
     * @throws SectionNotFoundException
     */
    public function findSectionOfName(string $name): Section;

    /**
     * @return Section
     */
    public function add(array $section): Section;

    /**
     * Update an existing section
     * @param array $section
     * @return Section
     */
    public function update(array $section): Section;
}
