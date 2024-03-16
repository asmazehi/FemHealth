<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Required;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/produit')]
class ProduitController extends AbstractController
{
    #[Route('/', name: 'app_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    #[Route('/AffichageFront', name: 'app_produit_indexFront', methods: ['GET'])]
public function indexFront(Request $request, PaginatorInterface $paginator): Response
{
    $repository = $this->getDoctrine()->getRepository(Produit::class)->createQueryBuilder('p')
        ->getQuery();

    $pagination = $paginator->paginate(
        $repository,
        $request->query->getInt('page', 1), // Current page number
        3
    );

    return $this->render('produit/indexFront.html.twig', ['pagination' => $pagination]);
}

    

    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            $this->uploadImages($produit, $image);

            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
        
    }

    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_delete', methods: ['POST'])]
     public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
    if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
        $imageDirectory = $this->getParameter('kernel.project_dir') . '/public/assets/uploads/product/';
        $produit->removeImage($imageDirectory);
        $entityManager->remove($produit);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
}


    private function uploadImages(Produit $activity, $image): void
    {
        $destination = $this->getParameter('kernel.project_dir') . '/public/assets/uploads/product';

        
            $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $slugger = new AsciiSlugger();
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

            $image->move($destination, $newFilename);

            $activity->setImage($newFilename);
    }


    #[Route('/categorie/{categorie}', name: 'produit_list_by_categorie')]
public function listByCategorie(string $categorie, EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
{
    $queryBuilder = $entityManager->getRepository(Produit::class)->createQueryBuilder('p');

    $queryBuilder->andWhere('p.Categorie = :categorie')
        ->setParameter('categorie', $categorie);

    $query = $queryBuilder->getQuery();

    $pagination = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1), // Current page number
        3 // Number of items per page
    );

    return $this->render('produit/indexFront.html.twig', [
        'pagination' => $pagination,
    ]);
}

#[Route('/searchProducts', name: 'searchProducts')]
public function searchProducts(Request $request, EntityManagerInterface $entityManager): Response
{
    $searchQuery = $request->query->get('search_query');

    $produit = $entityManager->getRepository(Produit::class)
        ->createQueryBuilder('u')
        ->where('u.Nom LIKE :query')
        ->orWhere('u.Categorie LIKE :query')
        ->orWhere('u.Description LIKE :query')
        ->setParameter('query', '%' . $searchQuery . '%')
        ->getQuery()
        ->getResult();

    return $this->render('produit/index.html.twig', [
        'produits' => $produit,
    ]);
}

}
