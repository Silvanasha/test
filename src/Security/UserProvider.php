<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\EntityUserProvider;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProvider extends EntityUserProvider
{
    public function __construct(
        ManagerRegistry $registry,
        string $classOrAlias = 'App\Entity\User',
        string $property = 'email',
        string $managerName = null
    ) {
        parent::__construct($registry, $classOrAlias, $property, $managerName);
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = parent::loadUserByIdentifier($identifier);

        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        if (!$user->isActive()) {
            throw new UserNotFoundException('This account is not active.');
        }

        return $user;
    }
}
