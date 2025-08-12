<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class InvalidUnitDomain extends \Exception
{
    public function __construct(string $message = '')
    {
        parent::__construct($message);
    }
}
