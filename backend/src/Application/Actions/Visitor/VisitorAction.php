<?php

declare(strict_types=1);

namespace App\Application\Actions\Visitor;

use App\Application\Actions\Action;
use App\Domain\Visitor\VisitorRepository;
use Psr\Log\LoggerInterface;

abstract class VisitorAction extends Action
{
    protected VisitorRepository $visitorRepository;

    public function __construct(LoggerInterface $logger, VisitorRepository $visitorRepository)
    {
        parent::__construct($logger);
        $this->visitorRepository = $visitorRepository;
    }
}
