<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Form\Publication1Type;
use App\Repository\PublicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Knp\Component\Pager\PaginatorInterface;
#[Route('/publication')]
class PublicationController extends AbstractController
{
    #[Route('/', name: 'app_publication_index')]
    public function index(PublicationRepository $publicationRepository): Response
    {
        
        return $this->render('publication/index.html.twig', [
            'publications' => $publicationRepository->findAll(),
        ]);
    }
    #[Route('/front_index', name: 'app_front_index')]
    public function indexfront(Request $request, PaginatorInterface $paginator): Response
    {
        $repository = $this->getDoctrine()->getRepository(Publication::class)->findAll();
    
        $pagination = $paginator->paginate(
            $repository,
            $request->query->getInt('page', 1), // Current page number
            4 // Number of items per page
        );
    
        return $this->render('publication/front_index.html.twig', ['p' => $pagination]);
    }
    #[Route('/new', name: 'app_publication_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $publication = new Publication();
        $form = $this->createForm(Publication1Type::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer le téléchargement de l'image
            $image = $form->get('image')->getData();

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // Stocker le fichier dans le répertoire souhaité
                $newFilename = $originalFilename.'.'.$image->guessExtension();
                $image->move(
                    $this->getParameter('upload_directory'),
                    $newFilename
                );
                // Mettre à jour l'entité avec le chemin du fichier
                $publication->setImage($newFilename);
            }

            // Enregistrer l'entité dans la base de données
            $entityManager->persist($publication);
            $entityManager->flush();

            return $this->redirectToRoute('app_publication_index');
        }

        return $this->renderForm('publication/new.html.twig', [
            'publication' => $publication,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_publication_show')]
    public function show(Publication $publication): Response
    {
        return $this->render('publication/show.html.twig', [
            'publication' => $publication,
        ]);
    }
    #[Route('/{id}/frontShow', name: 'front_show')]
    public function frontShow(Publication $publication): Response
    {
        return $this->render('publication/front_show.html.twig', [
            'publication' => $publication,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_publication_edit')]
    public function edit(Request $request, Publication $publication, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Publication1Type::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer le téléchargement de la nouvelle image si elle est fournie
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                try {
                    // Générez un nom de fichier unique pour la nouvelle image
                    $newFilename = uniqid().'.'.$imageFile->guessExtension();

                    // Déplacez le fichier téléchargé vers le répertoire de destination
                    $imageFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );

                    // Mettez à jour l'entité Publication avec le nouveau chemin de l'image
                    $publication->setImage($newFilename);
                } catch (FileException $e) {
                    // Gérer l'exception si le déplacement du fichier échoue
                    $this->addFlash('error', 'Une erreur s\'est produite lors du téléchargement de l\'image.');
                    
                    
                    return $this->redirectToRoute('app_publication_edit', ['id' => $publication->getId()]);
                }
            }

            // Enregistrez les modifications dans la base de données
            $entityManager->flush();

            $this->addFlash('success', 'Publication modifiée avec succès.');

            return $this->redirectToRoute('app_publication_index');
        }

        return $this->renderForm('publication/edit.html.twig', [
            'publication' => $publication,
            'form' => $form,
        ]);
    }
    #[Route('/delete/{id}', name: 'app_publication_delete', methods: ['POST'])]
    public function delete(Request $request, Publication $publication, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$publication->getId(), $request->request->get('_token'))) {
            // Get the comments associated wisth this publication
            $comments = $publication->getCommentaires();

            // Check if there are comments associated with this publication
            if (!empty($comments)) {
                // Remove each comment from the database
                foreach ($comments as $comment) {
                    $entityManager->remove($comment);
                }
            }

            // Remove the publication
            $entityManager->remove($publication);
            $entityManager->flush();
            
            $this->addFlash('success', 'Publication and associated comments have been deleted successfully.');
        } else {
            $this->addFlash('error', 'Invalid CSRF token.');
        }

        return $this->redirectToRoute('app_publication_index');
    }
     
}
