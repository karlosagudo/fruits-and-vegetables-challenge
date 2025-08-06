<?php

declare(strict_types=1);

namespace App\Application\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[WithHttpStatus(Response::HTTP_NOT_ACCEPTABLE)]
final class AlreadyDeleted extends \Exception
{
    public function __construct(string $message = '')
    {
        parent::__construct($message);
    }
}
