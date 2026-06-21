<?php

declare(strict_types=1);

namespace App\Application\Actions\Section;
use Psr\Http\Message\ResponseInterface as Response;

class AddSectionAction extends SectionAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $section = $this->request->getParsedBody();
        $result = $this->sectionRepository->add($section);
        $this->logger->info("Section is being added.");
        return $this->respondWithData($result);
    }
}
