<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:mark-inactive-users')]
class MarkInactiveUsersCommand extends Command
{
    const NUMBER_OF_DAYS_FOR_INACTIVATION = 30;

    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Marks users as inactive if they have not logged in for 1 month.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inactiveUsers = $this->userRepository->findInactiveUsers(self::NUMBER_OF_DAYS_FOR_INACTIVATION);

        foreach ($inactiveUsers as $user) {
            $user->setIsActive(false);
            $this->entityManager->persist($user);
        }

        $this->entityManager->flush();

        $output->writeln(sprintf('Marked %d users as inactive.', count($inactiveUsers)));

        return Command::SUCCESS;
    }
}
