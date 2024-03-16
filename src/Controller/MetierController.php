<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Publication;
use Knp\Component\Pager\PaginatorInterface;
class MetierController extends AbstractController
{
    #[Route('/metier', name: 'app_metier')]
    public function index(Request $request ): Response
{
    $searchValue = $request->query->get('search');
    $dateFilter = $request->query->get('date');

    $publications = $this->getDoctrine()
    ->getRepository(Publication::class)
    ->findAll();

    if ($searchValue) {
        $publications = $this->filterBySearch($publications, $searchValue);
    }

    if ($dateFilter) {
        $publications = $this->filterByDate($publications, $dateFilter);
    }

    return $this->render('publication/index.html.twig', [
        'publications' => $publications,
    ]);
}

#[Route('/frontmetier', name: 'frontmetier')]
    public function front_index(Request $request ): Response
{
    $searchValue = $request->query->get('search');
    $dateFilter = $request->query->get('date');

    $publications = $this->getDoctrine()
    ->getRepository(Publication::class)
    ->findAll();
    if ($searchValue) {
        $publications = $this->filterBySearch($publications, $searchValue);
    }

    if ($dateFilter) {
        $publications = $this->filterByDate($publications, $dateFilter);
    }

    return $this->render('publication/front_index.html.twig', [
        'publications' => $publications,
    ]);
}
private function filterBySearch(array $publications, string $searchValue): array
{
    return array_filter($publications, function ($publication) use ($searchValue) {
        return stripos($publication->getTitre(), $searchValue) !== false || stripos($publication->getContenu(), $searchValue) !== false;
    });
}

private function filterByDate(array $publications, string $dateFilter): array
{
    return array_filter($publications, function ($publication) use ($dateFilter) {
        $getDatepub = $publication->getDatepub()->format('Y-m-d');
        return $getDatepub === $dateFilter;
    });
}
    #[Route('/publication/front', name: 'display_prod_front')]
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
}