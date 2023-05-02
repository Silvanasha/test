<?php

declare(strict_types=1);

namespace App\Utils\Traits;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

trait TemporaryPasswordTrait
{
    public function generateTemporaryPassword(int $length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $temporaryPassword = '';

        for ($i = 0; $i < $length; $i++) {
            $temporaryPassword .= $characters[rand(0, $charactersLength - 1)];
        }

        return $temporaryPassword;
    }

    public function setTemporaryPassword(User $user, UserPasswordHasherInterface $passwordHasher): string
    {
        $temporaryPassword = $this->generateTemporaryPassword();

        $encodedPassword = $passwordHasher->hashPassword($user, $temporaryPassword);
        $user->setPassword($encodedPassword);

        return $temporaryPassword;
    }
}
