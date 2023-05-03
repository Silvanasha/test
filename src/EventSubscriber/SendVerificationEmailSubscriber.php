<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\UserRegisteredEvent;
use App\Security\EmailVerifier;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mime\Address;

class SendVerificationEmailSubscriber implements EventSubscriberInterface
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[ArrayShape([UserRegisteredEvent::class => 'string'])]
    public static function getSubscribedEvents(): array
    {
        return [
            UserRegisteredEvent::class => 'sendVerificationEmail',
        ];
    }

    public function sendVerificationEmail(UserRegisteredEvent $event): void
    {
        $user = $event->getUser();

        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address('no-reply@test.com', 'Mail Bot'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
    }
}
