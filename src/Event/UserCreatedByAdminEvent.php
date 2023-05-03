<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UserCreatedByAdminEvent extends Event
{
    public function __construct(private User $user, private string $temporaryPassword)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getTemporaryPassword(): string
    {
        return $this->temporaryPassword;
    }
}
