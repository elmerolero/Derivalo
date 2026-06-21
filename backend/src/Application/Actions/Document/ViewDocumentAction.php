<?php

declare(strict_types=1);

namespace App\Application\Actions\Document;
use Psr\Http\Message\ResponseInterface as Response;
use League\CommonMark\CommonMarkConverter;

class ViewDocumentAction extends DocumentAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $slug = $this -> args['slug'];
        $articlePath = __DIR__ . "\\..\\..\\..\\..\\public\\documents\\{$slug}.html";
        $articleLayout = __DIR__ . "\\..\\..\\..\\..\\media\\layout.html";
        
        if(!file_exists($articleLayout)) {
            $this -> response-> getBody() -> write("An error has ocurred.");
            return $this -> response->withStatus(500);
        }

        // Prefer pre-generated static HTML if available
        $articleInnerHtml = '';
        if(!file_exists($articlePath)) {
            $this -> response-> getBody() -> write("Document not found" . $articlePath);
            return $this -> response->withStatus(404);
        }
        
        $layout = file_get_contents($articleLayout);
        $article = file_get_contents($articlePath);
        $article = str_replace("{article}", $article, $layout);
        
        $this->response->getBody()->write($article);
        return $this->response;
    }
}