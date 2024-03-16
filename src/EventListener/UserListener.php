<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UserListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof User) {
            return;
        }

        // Logique pour envoyer un e-mail à l'administrateur
        // Utilisez $this->mailer pour envoyer l'e-mail
    }
}
