<?php

declare(strict_types=1);

namespace App\Application\Actions\Document;
use Psr\Http\Message\ResponseInterface as Response;

class ViewDocumentAction extends DocumentAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        // Load the article layout
        $articleLayout = "../media/layout.html";
        if(!file_exists($articleLayout)) {
            $this -> response-> getBody() -> write("An error has ocurred.");
            return $this -> response->withStatus(500);
        }
        $layout = file_get_contents($articleLayout);

        // Looks for the document in database
        $id = (int)$this -> args['id'];
        try {
            $document = $this -> documentRepository -> findDocumentOfId($id);
        }
        catch( \Throwable $e ) {
            $error = str_replace("{article}", "Document not found.", $layout);
            $this -> response-> getBody() -> write($error);
            return $this -> response -> withStatus(404);
        }

        // Looks for the HTML version of the article
        $articlePath = "./documents/{$document['file']}";
        if(!file_exists($articlePath)) {
            $error = str_replace("{article}", "Document not found.", $layout);
            $this -> response-> getBody() -> write($error);
            return $this -> response->withStatus(404);
        }

        // Generates the article
        $article = file_get_contents($articlePath);
        $article = str_replace("{article}", $article, $layout);
        $article = str_replace("{date}", $document['last_update'], $article);
        $article = str_replace("{author}", $document['author'], $article);
        $article = str_replace("{title}", $document['name'], $article);
        $this->response->getBody()->write($article);
        return $this->response;
    }
}