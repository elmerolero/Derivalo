<?php

declare(strict_types=1);

namespace App\Application\Actions\Document;

use App\Application\Actions\Action;
use Psr\Log\LoggerInterface;
use App\Domain\Section\SectionRepository;

abstract class DocumentAction extends Action
{
    protected SectionRepository $sectionRepository;

    public function __construct(LoggerInterface $logger, SectionRepository $sectionRepository)
    {
        parent::__construct($logger);
        $this -> sectionRepository = $sectionRepository;
    }
}
