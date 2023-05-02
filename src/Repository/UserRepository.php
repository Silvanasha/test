<?php

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findInactiveUsers(int $days)
    {
        $lastActiveDate = new \DateTimeImmutable(sprintf('-%d days', $days));

        $qb = $this->createQueryBuilder('u')
            ->where('u.lastLoginAt < :last_active_date')
            ->andWhere('u.isActive = :is_active')
            ->setParameter('last_active_date', $lastActiveDate)
            ->setParameter('is_active', true);

        return $qb->getQuery()->getResult();
    }
}
