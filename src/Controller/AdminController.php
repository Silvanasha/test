<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Event\UserCreatedByAdminEvent;
use App\Event\UserRegisteredEvent;
use App\Form\UserFormType;
use App\Utils\Traits\TemporaryPasswordTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    use TemporaryPasswordTrait;

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/users', name: 'app_users')]
    public function getUsers(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $users = $this->entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/user_list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/create_user', name: 'app_new_user')]
    public function createUser(
        Request $request,
        EventDispatcherInterface $dispatcher,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = new User();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $temporaryPassword = $this->setTemporaryPassword($user, $passwordHasher);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $userRegisteredEvent = new UserRegisteredEvent($user);
            $dispatcher->dispatch($userRegisteredEvent);

            $userCreatedByAdminEvent = new UserCreatedByAdminEvent($user, $temporaryPassword);
            $dispatcher->dispatch($userCreatedByAdminEvent);

            return $this->redirectToRoute('app_users');
        }

        return $this->render('admin/create_user.html.twig', [
            'userForm' => $form->createView(),
        ]);
    }
}
