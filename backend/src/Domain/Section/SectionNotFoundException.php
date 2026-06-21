<?php

declare(strict_types=1);

namespace App\Domain\Section;

use App\Domain\DomainException\DomainRecordNotFoundException;

class SectionNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'The section you requested does not exist.';
}
