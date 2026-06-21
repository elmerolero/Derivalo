<?php

declare(strict_types=1);

namespace App\Application\Actions\Visitor;

use Psr\Http\Message\ResponseInterface as Response;
use App\Domain\Visitor\Visitor;

class AddVisitorAction extends VisitorAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $this->logger->info("New visitor was added.");
        $data = $this->request->getParsedBody();
        
        $userAgent = $this->request->getHeaderLine('User-Agent');
        $ip = $this->request->getServerParams()['REMOTE_ADDR'];
        $timestamp = date("Y-m-d H:i:s");
       
        $visitor = $this->visitorRepository->add($userAgent, $ip, $timestamp);

        return $this->respondWithData($visitor);
    }
}
