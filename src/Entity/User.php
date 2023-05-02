<?php

declare(strict_types = 0);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 25, nullable: false)]
    #[Assert\NotNull]
    private string $name;

    #[ORM\Column(type: 'string', length: 254, unique: true, nullable: false)]
    #[Assert\NotNull]
    private string $email;

    #[ORM\Column(type: 'string', length: 64, nullable: false)]
    private string $password;

    #[ORM\Column(type: 'boolean', nullable: false)]
    #[Assert\NotNull]
    private bool $isVerified = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private \DateTime $lastLoginAt;

    #[ORM\Column(type: 'boolean', nullable: false)]
    #[Assert\NotNull]
    private bool $isActive = true;

    public function getId() : int
    {
        return $this->id;
    }

    public function getRoles() : array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials()
    {
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail() : string
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function setLastLoginAt(\DateTime $lastLoginAt)
    {
        $this->lastLoginAt = $lastLoginAt;
    }

    public function updateLastLoginAt(): self
    {
        $this->setLastLoginAt(new \DateTime());

        return $this;
    }

    public function getLastLoginAt(): \DateTime
    {
        return $this->lastLoginAt;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive)
    {
        $this->isActive = $isActive;
    }
}
