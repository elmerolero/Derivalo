<?php

declare(strict_types=1);

namespace App\Application\Actions\Document;
use Psr\Http\Message\ResponseInterface as Response;
use App\Domain\Document\DocumentRepository;
use Psr\Log\LoggerInterface;

class ListDocumentsBySectionAction extends DocumentAction
{
    /**
     * {@inheritdoc}
     */
    private DocumentRepository $documentRepository;

    public function __construct(LoggerInterface $logger, \App\Domain\Section\SectionRepository $sectionRepository, DocumentRepository $documentRepository)
    {
        parent::__construct($logger, $sectionRepository);
        $this->documentRepository = $documentRepository;
    }
    protected function action(): Response
    {
        $sectionParam = $this->args['section'];

        if (!is_numeric($sectionParam)) {
            return $this->respondWithData([], 400);
        }

        $sectionId = (int)$sectionParam;

        $documents = $this->documentRepository->findBySection($sectionId);

        $this->logger->info("Document list for section id {$sectionId} was viewed.");

        return $this->respondWithData($documents);
    }
}
