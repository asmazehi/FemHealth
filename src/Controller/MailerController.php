<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerController extends AbstractController
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function register(Request $request, $user)
    {
        // Process registration form...

        // Supposons que vous obteniez l'utilisateur à partir du formulaire ou de toute autre méthode
        // Send welcome email


        try {
            $userEmail = $user->getEmail();
            // $this->sendWelcomeEmail($userEmail);
            $email = (new Email())
                ->from('your_email@example.com')
                ->to($userEmail)
                ->subject('Welcome to Our Application')
                ->html('<p>Welcome to Our Application! We are excited to have you on board.</p>');
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
        }

        // Redirection ou affichage d'un message de succès
    }

//    private function sendWelcomeEmail($userEmail)
//    {
//        $email = (new Email())
//            ->from('your_email@example.com')
//            ->to($userEmail)
//            ->subject('Welcome to Our Application')
//            ->html('<p>Welcome to Our Application! We are excited to have you on board.</p>');
//
//        $this->mailer->send($email);
//    }
}
