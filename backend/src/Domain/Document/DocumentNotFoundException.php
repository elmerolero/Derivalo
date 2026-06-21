<?php

declare(strict_types=1);

namespace App\Domain\Document;

use App\Domain\DomainException\DomainRecordNotFoundException;

class DocumentNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'The document you requested does not exist.';
}
