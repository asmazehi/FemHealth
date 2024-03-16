<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface; //interagir avec l'entitéet la base de donne
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; //importer la classe request 
use Symfony\Component\HttpFoundation\Response; //importer la classe response 
use Symfony\Component\Routing\Annotation\Route; //acceder a la classe route 
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


#[Route('/evenement')]
class EvenementController extends AbstractController    //admin
{
    #[Route('/', name: 'app_evenement_index', methods: ['GET'])]
    public function index(EvenementRepository $evenementRepository): Response
    {
        
        return $this->render('evenement/index.html.twig', [
            'evenement' => $evenementRepository->findAll(),//elle passe tous les événements récupérés à partir du EvenementRepository.
        ]);
    }


    #[Route('/indexFront', name: 'app_evenement_indexFront', methods: ['GET','POST'])]    //front
    public function indexFront(EvenementRepository $evenementRepository,Request $request): Response
    { if ($request->isMethod('POST')) {

         $searchQuery = $request->request->get('nom');
     
       $evenement = $evenementRepository->findByNom($searchQuery);
       return $this->render('evenement/indexFront.html.twig', [
        'evenement' => $evenement,
    ]);
    }
    if ($request->isMethod('GET')) { //hedhi lfaza ta annuler tawed twarik les events lkol

       
      return $this->render('evenement/indexFront.html.twig', [
        'evenement' => $evenementRepository->findAll(),
   ]);
   }
        return $this->render('evenement/indexFront.html.twig', [
            'evenement' => $evenementRepository->findAll(),
        ]);
    }


    
   

    #[Route('/new', name: 'app_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($evenement);//enregistre l'objet evenement dans la base de données en utilisant l'EntityManager
            $entityManager->flush();//fai reelement l'action

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_show', methods: ['GET'])]
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }





   #[Route('/{id}/details', name: 'detailevent', methods: ['GET'])]
public function show1($id, EvenementRepository $evenementRepository): Response
{
    $evenement = $evenementRepository->find($id);

    if (!$evenement) {
        throw $this->createNotFoundException('L\'événement n\'a pas été trouvé');
    }

    return $this->render('evenement/detailevent.html.twig', [
        'evenement' => $evenement,

    ]);
}



    
    #[Route('/{id}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_delete', methods: ['POST'])]
    public function delete(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evenement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/listevent', name: 'listevent')]
    public function list(EvenementRepository $evenementRepository, Request $request): Response
    {
        $evenement = $evenementRepository->findAll();
    
        return $this->render('evenement/indexFront.html.twig', [
            'evenement' => $evenement,
        ]);
    }
#[Route('/listevent/{typeId}', name: 'listevent1')] //lista filtered by id type
public function list1(Request $request, int $typeId, EntityManagerInterface $entityManager): Response
    {
        $evenement = $entityManager->getRepository(Evenement::class)->findBy(['type' => $typeId]);

        return $this->render('evenement/indexFront1.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/signaler/{id}', name: 'evenement_signaler')]
    public function signalerEvenement(Evenement $evenement, Request $request, EntityManagerInterface $entityManager): Response
    {
        $evenement->incrementSignaler(); // Assurez-vous que vous avez une méthode incrementSignaler() dans votre entité Evenement
        $entityManager->flush();
        
        if ($evenement->getSignaler() >= 3) {
            $entityManager->remove($evenement);
            $entityManager->flush();
            $this->addFlash('success', 'L\'événement a été supprimé en raison de signalements répétés.');
        } else {
            $this->addFlash('success', 'L\'événement a été signalé avec succès.');
        }
    
        return $this->redirectToRoute('app_evenement_indexFront'); // Assurez-vous de rediriger vers la route appropriée
    }
    


    #[Route('/evenement/montant/{montant}', name: 'evenement_list_by_montant')]

    public function listByMontant(Request $request, int $montant, EntityManagerInterface $entityManager): Response
    {
        $queryBuilder = $entityManager->getRepository(Evenement::class)->createQueryBuilder('e');
    
        if ($montant < 0) {
            $queryBuilder->andWhere('e.montant < :montant')
                ->setParameter('montant', -$montant);
        } elseif ($montant > 0) {
            $queryBuilder->andWhere('e.montant > :montant')
            ->setParameter('montant', $montant);
        }
    
        $evenement = $queryBuilder->getQuery()->getResult();
    
        return $this->render('evenement/indexFront1.html.twig', [
            'evenement' => $evenement,
        ]);
    }


//tekhdemch

    #[Route('/evenement_nom/{nom}', name : 'evenement_nom')]

    public function evenement(string $nom, EntityManagerInterface $entityManager): Response
    {
        $evenement = $entityManager->getRepository(Evenement::class)
            ->createQueryBuilder('e')
            ->where('e.nom = :nom')
            ->setParameter('nom', $nom)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$evenement) {
            throw $this->createNotFoundException('No evenement found for nom "' . $nom . '".');
        }

        // Do something with the Evenement object

        return $this->render('evenement/indexFront.html.twig', [
            'evenement' => $evenement,
        ]);
    }





















    #[Route('/calendar', name: 'calendarpub')]
    public function calendar(EvenementRepository $evenementRepository): Response
    {
        $evenements = $evenementRepository->findAll();

        $rdvs = [];

        foreach ($evenements as $event) {
            $rdvs[] = [
                'id' => $event->getId(),
                'title' => $event->getNom(),
                'start' => $event->getDateDebut()->format('Y-m-d'),
                'end' => $event->getDateFin()->format('Y-m-d'),
                'description' => $event->getLocalisation()
            ];
        }

        $data = json_encode($rdvs);

        return $this->render('evenement/test2.html.twig', compact('data'));
    }
}