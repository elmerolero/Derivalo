<?php

declare(strict_types=1);

namespace App\Application\Actions\Document;
use Psr\Http\Message\ResponseInterface as Response;

class ListDocumentsBySectionAction extends DocumentAction
{
    protected function action(): Response
    {
        $sectionParam = $this->args['section'];

        if (!is_numeric($sectionParam)) {
            return $this->respondWithData([], 400);
        }

        $sectionId = (int)$sectionParam;
        try {
            $documents = $this->documentRepository -> findBySection($sectionId);
        }
        catch(\Throwable $e) {
            $this -> response -> getBody() -> write(json_encode(['error' => 'Document not found']));
            return $this -> response -> withStatus(404);
        }
        return $this->respondWithData($documents);
    }
}
