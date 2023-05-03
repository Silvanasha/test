<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\UserCreatedByAdminEvent;
use JetBrains\PhpStorm\ArrayShape;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class SendUserLoginInformationEmailSubscriber implements EventSubscriberInterface
{
    public function __construct(private MailerInterface $mailer, private LoggerInterface $logger)
    {
    }

    #[ArrayShape([UserCreatedByAdminEvent::class => 'string'])]
    public static function getSubscribedEvents(): array
    {
        return [
            UserCreatedByAdminEvent::class => 'onUserCreatedByAdmin',
        ];
    }

    public function onUserCreatedByAdmin(UserCreatedByAdminEvent $event): void
    {
        $user = $event->getUser();
        $temporaryPassword = $event->getTemporaryPassword();

        $email = (new TemplatedEmail())
                ->from(new Address('no-reply@test.com', 'Mail Bot'))
        ->to($user->getEmail())
        ->subject('Login information email')
        ->htmlTemplate('registration/login_information_email.html.twig')
        ->context([
            'temporaryPassword' => $temporaryPassword,
            'userName' => $user->getName(),
            'userEmail' => $user->getEmail(),
        ]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $errorMessage = sprintf('Unable to send login information to user %s: %s', $user->getEmail(), $e->getMessage());
            $this->logger->error($errorMessage);
        }
    }
}
