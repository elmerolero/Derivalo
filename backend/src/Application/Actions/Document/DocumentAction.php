<?php

declare(strict_types=1);

namespace App\Application\Actions\Document;

use App\Application\Actions\Action;
use App\Domain\Document\DocumentRepository;
use Psr\Log\LoggerInterface;
use App\Domain\Section\SectionRepository;
use App\Domain\User\UserRepository;

abstract class DocumentAction extends Action
{
    protected DocumentRepository $documentRepository;
    protected SectionRepository $sectionRepository;
    protected UserRepository $userRepository;

    public function __construct(
        LoggerInterface $logger, 
        DocumentRepository $documentRepository, 
        SectionRepository $sectionRepository,
        UserRepository $userRepository )
    {
        parent::__construct($logger);
        $this -> documentRepository = $documentRepository;
        $this -> sectionRepository = $sectionRepository;
        $this -> userRepository = $userRepository;
    }
}
