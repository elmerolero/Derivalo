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
     * @return Section
     */
    public function add(array $section): Section;
}
