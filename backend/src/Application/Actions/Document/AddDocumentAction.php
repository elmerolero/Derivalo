<?php

declare(strict_types=1);

namespace App\Application\Actions\Document;

use Psr\Http\Message\ResponseInterface as Response;
use League\CommonMark\CommonMarkConverter;
use InvalidArgumentException;
use App\Domain\Document\DocumentRepository;

class AddDocumentAction extends DocumentAction
{
    /**
     * {@inheritdoc}
     */
    private DocumentRepository $documentRepository;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \App\Domain\Section\SectionRepository $sectionRepository,
        DocumentRepository $documentRepository
    ) {
        parent::__construct($logger, $sectionRepository);
        $this->documentRepository = $documentRepository;
    }

    protected function action(): Response
    {
        // Accept either JSON body with slug+markdown or multipart file upload
        $data = $this -> request -> getParsedBody();
        $uploaded = $this -> request -> getUploadedFiles();
        $slug = null;
        $markdown = null;

        // Validate name
        $name = isset($data['name']) ? filter_var($data['name']) : '';
        if(empty($name)) {
            $this -> response -> getBody() -> write(json_encode(['error' => 'You must set a valid name.']));
            return $this -> response -> withStatus(400);
        }

        // Description
        $description = isset($data['description']) ? filter_var($data['description']) : '';

        // File
        if(empty($uploaded) || !isset($uploaded['file'])){
            // Request upload a file
            $this -> response -> getBody() -> write(json_encode(['error' => 'You must upload a file.']));
            return $this -> response -> withStatus(400);
        }

        // Section
        $fkSection = isset($data['fk_section']) ? filter_var($data['fk_section'], FILTER_VALIDATE_INT) : '';
        if(empty($fkSection)) {
            $this -> response -> getBody() -> write(json_encode(['error' => 'You must set a section.']));
            return $this -> response -> withStatus(400);
        }

        // Keep the original markdown files in media/articles
        $file = $uploaded['file'];
        if ($file->getError() === UPLOAD_ERR_OK) {
            $stream = $file->getStream();
            $markdown = $stream->getContents();
        }

        $mdDir = __DIR__ . "\\..\\..\\..\\..\\media\\articles\\";
        if (!is_dir($mdDir)) {
            mkdir($mdDir, 0755, true);
        }

        $mdPath = $mdDir . "{$name}.md";
        file_put_contents($mdPath, $markdown);

        // Write generated HTML into public/documents for static serving
        $htmlDir = __DIR__ . "\\..\\..\\..\\..\\public\\documents\\";
        if (!is_dir($htmlDir)) {
            mkdir($htmlDir, 0755, true);
        }

        $htmlPath = $htmlDir . "{$name}.html";

        $converter = new CommonMarkConverter();
        $html = $converter->convert($markdown);

        // Wrap converted HTML in a minimal template linking the shared CSS
        file_put_contents($htmlPath, $html);

        // register in DB (use name as slug)
        try {
            $doc = [
                'name' => $name,
                'description' => $description,
                'fk_section' => $fkSection,
                'available' => 1,
                'file' => "{$name}.html"
            ];
            $added = $this->documentRepository->add($doc);
        }
        catch (\Throwable $e) {
            $this->logger->info('Could not register document in DB: ' . $e->getMessage());
            $added = null;
        }

        $this->logger->info("Document {$slug} added.");

        return $this->respondWithData([
            'name' => $name,
            'md' => $mdPath,
            'html' => $htmlPath,
            'db' => $added
        ]);
    }
}
