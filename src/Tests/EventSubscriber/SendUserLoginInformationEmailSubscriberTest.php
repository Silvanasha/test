<?php

declare(strict_types=1);

namespace App\Tests\EventSubscriber;

use App\Entity\User;
use App\Event\UserCreatedByAdminEvent;
use App\EventSubscriber\SendUserLoginInformationEmailSubscriber;
use App\Utils\Traits\TemporaryPasswordTrait;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;

class SendUserLoginInformationEmailSubscriberTest extends TestCase
{
    use TemporaryPasswordTrait;

    private SendUserLoginInformationEmailSubscriber $subscriber;
    private MailerInterface $mailer;

    protected function setUp(): void
    {
        $this->mailer = $this->createMock(MailerInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $this->subscriber = new SendUserLoginInformationEmailSubscriber($this->mailer, $logger);
    }

    public function testGetSubscribedEvents()
    {
        $this->assertEquals([
            UserCreatedByAdminEvent::class => 'onUserCreatedByAdmin',
        ], $this->subscriber::getSubscribedEvents());
    }

    public function testOnUserCreated()
    {
        $user = $this->createUser();

        $temporaryPassword = $this->generateTemporaryPassword();

        $event = new UserCreatedByAdminEvent($user, $temporaryPassword);

        $this->mailer->expects($this->once())
            ->method('send');

        $this->subscriber->onUserCreatedByAdmin($event);
    }

    private function createUser(): User
    {
        $user = new User();
        $user->setName('user');
        $user->setEmail('user@example.com');

        return $user;
    }
}
