<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Form\Commentaire1Type;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PublicationRepository;
use App\Entity\Publication;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
#[Route('/commentaire')]
class CommentaireController extends AbstractController
{
    #[Route('/', name: 'app_commentaire_index')]
    public function index(CommentaireRepository $commentaireRepository): Response
    {
        $commentaires = $this->getDoctrine()->getRepository(Commentaire::class)->findAll();
        return $this->render('commentaire/index.html.twig', [
            'commentaires' => $commentaireRepository->findAll(),
        ]);
    }
    
    #[Route('/new/{id}', name: 'app_commentaire_new')]
    public function new(int $id, Request $request, EntityManagerInterface $entityManager, PublicationRepository $publicationRepository): Response
    {
        // Fetch the associated publication
        $publication = $publicationRepository->find($id);
        if (!$publication) {
            throw $this->createNotFoundException('No publication found for id '.$id);
        }
    
        // Create a new Commentaire entity and associate it with the publication
        $commentaire = new Commentaire();
        $commentaire->setPublication($publication);
        
        // Set the default value of 'like' attribute to 0
        $commentaire->setLikes(0);
    
        // Set the user associated with the comment
        $user = $this->getUser(); // Assuming you are using Symfony's security component
        if ($user instanceof UserInterface) {
            $commentaire->setUser($user);
        } else {
            // Handle the case where user is not logged in or not available
            // You can also throw an exception if necessary
            // For example:
            throw new \RuntimeException('User not authenticated.');
        }
    
        $form = $this->createForm(Commentaire1Type::class, $commentaire);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commentaire);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_publication_show', ['id' => $publication->getId()]);
        }
    
        return $this->render('commentaire/new.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form->createView(), // Pass the form directly to the template
            'id' => $id,
        ]);
    }
    #[Route('/{id}/like', name: 'like_commentaire', methods: ['POST'])]
    public function likeCommentaire(Request $request, Commentaire $commentaire,EntityManagerInterface $entityManager): Response
    {
        //$commentaire->addLike(); // Ajouter un like au commentaire
        $commentaire->setLikes($commentaire->getLikes()+1);
        $entityManager->persist($commentaire);
        $entityManager->flush();
    
        return new JsonResponse(['success' => true]);
    }
    #[Route('/{id}/edit', name: 'app_commentaire_edit')]
    public function edit(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Commentaire1Type::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commentaire_index');
        }

        return $this->renderForm('commentaire/edit.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commentaire_delete')]
    public function delete(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commentaire->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commentaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commentaire_index');
    }

    /* Admin Functions */
    #[Route('/admin', name: 'frontindex')]
    public function frontindex(CommentaireRepository $commentaireRepository): Response
    {
        return $this->render('commentaire/frontindex.html.twig', [
            'commentaires' => $commentaireRepository->findAll(),
        ]);
    }
    
    #[Route('/admin/activeComment/{id}', name:'app_activation_comment')]
    public function activeComment(Commentaire $commentaire, EntityManagerInterface $entityManager, Request $request, MailerInterface $mailer): Response
    {
         // Récupérer l'état actuel du commentaire
    $isActive = $commentaire->isActive();

    // Récupérer la nouvelle valeur de l'état actif à partir de la requête
    $newActiveState = $request->request->get('actif') === "actif";

    // Si l'état actuel est différent de la nouvelle valeur et que le nouveau état est inactif, envoyer l'e-mail
    if ($isActive !== $newActiveState && !$newActiveState) {
        // Récupérer l'utilisateur associé au commentaire
        $user = $commentaire->getUser();

        // Vérifier si l'utilisateur existe avant d'envoyer un email
        if ($user instanceof User) {
            // Envoyer un email à l'utilisateur
            $email = (new Email())
                ->from('votre@email.com')
                ->to($user->getEmail())
                ->subject('Désactivation de votre commentaire')
                ->html('<p>Votre commentaire a été désactivé.</p>');

            $mailer->send($email);
        }
    }
    else{
        $user = $commentaire->getUser();

        // Vérifier si l'utilisateur existe avant d'envoyer un email
        if ($user instanceof User) {
            // Envoyer un email à l'utilisateur
            $email = (new Email())
                ->from('votre@email.com')
                ->to($user->getEmail())
                ->subject('Aactivation de votre commentaire')
                ->html('<p>Votre commentaire a été activé.</p>');

            $mailer->send($email);
        }
    }

    // Mettre à jour l'état du commentaire
    $commentaire->setActive($newActiveState);

    $entityManager->flush();

    return $this->redirectToRoute('app_commentaire_index'); 
    }
    public function triCroissant(CommentaireRepository $commentaireRepository): Response
    {
        $commentaires = $commentaireRepository->findAllSortedByDate();

        return $this->render('commentaire/index.html.twig', [
            'commentaires' => $commentaires,
        ]);
    }
}
