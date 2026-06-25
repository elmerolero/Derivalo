<?php

declare(strict_types=1);

namespace App\Application\Actions\Document;

use Dotenv\Exception\InvalidFileException;
use Psr\Http\Message\ResponseInterface as Response;
use League\CommonMark\CommonMarkConverter;

class AddDocumentAction extends DocumentAction
{
    protected function action(): Response
    {
        // Try to get user id from Bearer token
        $userId = $this -> request -> getAttribute('user');
        if(!isset($userId) || $userId === 0){
            return $this -> respondWithData([ 'pk_user' => 0, 'email' => ''], 401);
        }

        // Accept either JSON body with slug+markdown or multipart file upload
        $data = $this -> request -> getParsedBody();
        $uploaded = $this -> request -> getUploadedFiles();
        $markdown = null;

        $validation = $this -> validateDocumentData($data, $uploaded, (int)$userId);
        if(!$validation['valid']) {
            $this -> response -> getBody() -> write(json_encode(['error' => $validation['message']]));
            return $this -> response -> withStatus(400);
        }

        // Keep the original markdown files in media/articles
        $file = $validation['file'];
        if($file -> getError() !== UPLOAD_ERR_OK) {
            $this -> response -> getBody() -> write(json_encode(['error' => 'An error processing uploaded file has ocurred.']));
            return $this -> response -> withStatus(500);
        }

        // Checks that specific routes exists
        $mdDir = $_ENV['FILE_ARTICLE_MD_PATH'];
        $htmlDir = $_ENV['FILE_ARTICLE_HTML_PATH'];
        if (!is_dir($mdDir) || !is_dir($htmlDir)) {
            throw new InvalidFileException("The specified paths do not exist.");
        }

        // Generates filename
        $fileName = preg_replace('/[<>:"\/\\\\|?*\x00-\x1F]/', '_', $validation['name']);
        $fileName = strtolower(preg_replace('/\s+/', '_', $fileName));

        // Read uploaded file and checks it is not a binary file
        $stream = $file -> getStream();
        $markdown = $stream -> getContents();
        if (strpos($markdown, "\0") !== false || empty($markdown)) {
            $this -> response -> getBody() -> write(json_encode(['error' => 'Debes de cargar un archivo válido.']));
            return $this -> response -> withStatus(400);
        }
        
        $mdPath = $mdDir . "{$fileName}.md";
        $htmlPath = $htmlDir . "{$fileName}.html";
        if(file_exists($mdPath) || file_exists($htmlPath)) {
            $this -> response -> getBody() -> write(json_encode(['error' => 'Ya existe un artículo con ese nombre.']));
            return $this -> response -> withStatus(400);
        }

        // Convert the markdown file
        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false
        ]);
        $html = $converter->convert($markdown);
        
        // Saves the markdown file
        $mdSaved = file_put_contents($mdPath, $markdown);
        if($mdSaved === false){
            $this -> response -> getBody() -> write(json_encode(['error' => 'Ocurrió un error al cargar el archivo actual.']));
            return $this -> response -> withStatus(500);
        }

        // Saves the html file
        $htmlSaved = file_put_contents($htmlPath, $html);
        if($htmlSaved === false){
            if(file_exists($mdPath)) unlink($mdPath);
            $this -> response -> getBody() -> write(json_encode(['error' => 'Ocurrió un error al cargar el archivo actual.']));
            return $this -> response -> withStatus(500);
        }

        // register in DB (use name as slug)
        try {
            $doc = [
                'name' => $validation['name'],
                'description' => $validation['description'],
                'fk_section' => $validation['section'],
                'available' => 1,
                'file' => "{$fileName}.html",
                'userId' => $validation['userId']
            ];

            $added = $this -> documentRepository -> add($doc);
        }
        catch (\Throwable $e) {
            if(file_exists($mdPath)) unlink($mdPath);
            if(file_exists($htmlPath)) unlink($htmlPath);
            $this -> logger -> info('Could not register document in DB: ' . $e->getMessage());
            $this -> response -> getBody() -> write(json_encode(['error' => 'Ocurrió un error al cargar el archivo actual.']));
            return $this -> response -> withStatus(500);
        }

        $this -> logger -> info("Document {$validation['name']} added.");

        return $this->respondWithData([
            'name' => $validation['name'],
            'md' => $mdPath,
            'html' => $htmlPath,
            'db' => $added
        ]);
    }

    private function validateDocumentData(array $data, array $uploaded, int $userId): array {
        // Checks for existing user id
        try {
            $user = $this -> userRepository -> findUserOfId($userId);
        }
        catch(\Throwable $e) {
            return ['valid' => false, 'message' => 'Acceso no válido.'];
        }

        // Validate name
        $name = isset($data['name']) ? preg_replace('/[^\p{L}\p{N}\s._-]+/u', '', $data['name']) : '';
        if(empty($name)) {
            return ['valid' => false, 'message' => 'Debes establecer un nombre válido.'];
        }

        // Description
        $description = isset($data['description']) ? preg_replace('/[\x00-\x1F\x7F]/u', '', $data['description']) : '';

        // File
        if(empty($uploaded) || !isset($uploaded['file'])){
            // Request upload a file
            return ['valid' => false, 'message' => 'Debes de cargar un archivo.'];
        }
        
        try {
            // Section
            $fkSection = isset($data['fk_section']) ? (int)filter_var($data['fk_section'], FILTER_VALIDATE_INT) : '';
            if(empty($fkSection)) {
                return ['valid' => false, 'message' => 'Debes seleccionar una sección válida.'];
            }

            $section = $this -> sectionRepository -> findSectionOfId($fkSection);
        }
        catch(\Throwable $e) {
            return ['valid' => false, 'message' => 'Debes seleccionar una sección válida.'];
        }

        return [
            'valid' => true,
            'message' => '',
            'name' => $name,
            'description' => $description,
            'file' => $uploaded['file'],
            'section' => $section -> getPkSection(),
            'userId' => $user -> getId()
        ];
    }
}
