<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\EditProfileFormType;
use App\Repository\PublicationRepository;
use App\Repository\SponsorRepository;

class DashboardController extends AbstractController
{

    /**
     * @Route("/", name="app_dashboard_front")
     */
    public function index(): Response
    {
        return $this->render('dashboard/front.html.twig');
    }
    #[Route('/dashbord', name: 'app_dashbord')]
    public function dashbord(SponsorRepository $sponsorRepository ,PublicationRepository $publicationRepository): Response
    {
        $stats = $sponsorRepository->findSponsorWithMostProducts();
        $publicationStats = $publicationRepository->findPublicationWithCommentCount();
        return $this->render('dashboard/index.html.twig', [
            'stats' => $stats , 'publicationStats' => $publicationStats,
        ]);
    }
    /**
     * @Route("/admin", name="app_dashboard_admin")
     */
    public function indexAdmin(MailerInterface $mailer): Response
    {

        return $this->render('dashboard/index.html.twig');
    }

    /**
     * @Route("/admin/users", name="app_users_admin")
     */
    public function usersAdmin(UserRepository $userRepository): Response
    {
        $date = new \DateTime();
        $stats = $userRepository->usersForStat($date);
        $users = $userRepository->findAll();
        return $this->render('dashboard/users.html.twig', ['users' => $users, 'stats' => json_encode($stats)]);
    }

    /**
     * @Route("/admin/changeRole/{id}", name="app_change_role")
     */
    public function changeRoleAdmin(User $user, EntityManagerInterface $entityManager, Request $request): Response
    {
        $roleSelected = $request->request->get('role');
        $user->setRoles([$roleSelected]);
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('app_users_admin');
    }

    /**
     * @Route("/admin/activeUser/{id}", name="app_activation_user")
     */
    public function activeUser(User $user, EntityManagerInterface $entityManager, Request $request, MailerInterface $mailer): Response
    {
        $active = $request->request->get('actif');
        $isActive = $active == "actif" ? true : false;
        $user->setActive($isActive);
        $entityManager->persist($user);


        $entityManager->flush();
        if (!$isActive) {
            // L'utilisateur a été désactivé, envoyez un e-mail de notification
            $email = (new Email())
                ->from('contact@femHealth.com')
                ->to($user->getEmail())
                ->subject('Votre compte a été désactivé')
                ->html('<p>Votre compte a été désactivé pour le moment. Si vous avez des questions, veuillez nous contacter à contact@femHealth.com.</p>');
            $mailer->send($email);
        }
        return $this->redirectToRoute('app_users_admin');
    }

    /**
     * @Route("/edit-profile", name="edit_profile")
     */
    public function editProfile(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        $form = $this->createForm(EditProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès.');

            return $this->redirectToRoute('edit_profile');
        }

        return $this->render('dashboard/edit_profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    //tri des utilisateurs :

    /**
     * @Route("/users/alphabetical", name="users_alphabetical")
     */
    public function listAlphabetical(UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy([], ['username' => 'ASC']);

        return $this->render('user/list.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/users/registration-date", name="users_registration_date")
     */
    public function listByRegistrationDate(Request $request, UserRepository $userRepository): Response
    {
        $date = $request->request->get('registrationDate');

        $users = $userRepository->findByDte($date);

        return $this->render('dashboard/users.html.twig', ['users' => $users, 'date' => $date]);
    }

}




