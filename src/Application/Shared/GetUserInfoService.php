<?php

declare(strict_types=1);

namespace App\Application\Shared;

use Symfony\Bundle\SecurityBundle\Security;

class GetUserInfoService
{
    public function __construct(private Security $security) {}

    public function getUserIdentifier(): ?string
    {
        return $this->security->getUser()?->getUserIdentifier() ?? null;
    }

    public function getUserNameHumanized(): ?string
    {
        return $this->getUserIdentifier();
    }
}
