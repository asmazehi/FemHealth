<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppCustomAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppCustomAuthenticator $authenticator, EntityManagerInterface $entityManager,MailerInterface $mailer ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            //Définition du rôle et de l'état actif de l'utilisateur :
            $user->setRoles(['ROLE_CLIENT']);
            $user->setActive(true);
            $user->setRegisteredAt(new \DateTime());
            //Persist et flush de l'entité utilisateur en base de données :
            $entityManager->persist($user);

            $entityManager->flush();
            //envoi mail
            $userEmail = $user->getEmail();
            // $this->sendWelcomeEmail($userEmail);
            $email = (new Email())
                ->from('contact@femHealth.com')
                ->to($user->getEmail())
                ->subject('Welcome to Our Application')
                ->html('<p>Welcome to Our Application! We are excited to have you on board.</p>');
            $this->mailer->send($email);

            // do anything else you need here, like send an email
            //Authentification de l'utilisateur nouvellement enregistré
            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }
        //Rendu du formulaire d'inscription en cas d'erreur
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
