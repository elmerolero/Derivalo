<?php

declare(strict_types=1);

namespace App\Application\Actions\Section;

use Psr\Http\Message\ResponseInterface as Response;

class UpdateSectionAction extends SectionAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $section = $this->request->getParsedBody();
        $result = $this->sectionRepository->update($section);
        $this->logger->info("Section updated.");
        return $this->respondWithData($result);
    }
}
