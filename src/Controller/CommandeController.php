<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Panier;
use App\Form\CommandType;
use App\Repository\CommandeRepository;
use App\Repository\PanierRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'app_commande')]
    public function index(): Response
    {
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
        ]);
    }


    #[Route('/dashboard/vente', name: 'dashboardvente')]
    public function dashboard(): Response
    {
        return $this->render('commande/dashbord.html.twig');}





    #[Route('/commandeshow/{idPanier}', name: 'showcommande')]
    public function commande(EntityManagerInterface $entityManager,$idPanier): Response
    {
        $panier  = $entityManager->getRepository(Panier::class)
            ->find($idPanier);

        if (!$panier) {
            throw $this->createNotFoundException(
                'Aucun panier trouvé pour cet ID: '.$idPanier
            );
        }
        $lignesPanier = $panier->getLignepaniers();
        $produits = [];

        foreach ($lignesPanier as $lignePanier) {
            // Utilisez une autre variable pour stocker chaque élément de la collection
            $produits[] = $lignePanier;
        }

        return $this->render('Commande/ShowCommande.html.twig', [
            'lignePanier' => $produits, // Utilisez la nouvelle variable $produits
            'idp' => $idPanier,
        ]);}



    #[Route('/commande/checkout1/{idPanier}', name: 'Command_add')]
    public function add(Request $request, $idPanier, ManagerRegistry $manager, PanierRepository $panierRepository): Response
    {
        $panier = $panierRepository->find($idPanier);
        $command = new Commande();

        $form = $this->createForm(CommandType::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command->setDateC(new \DateTime());
            $command->setStatut('en_cours');
            $command->setPanier($panier);
            $entityManager = $manager->getManager();
            $entityManager->persist($command);
            $entityManager->flush();
            if($command->getMethodePaiement()=="Cash on delivery"){
            return $this->redirectToRoute('showcommande', ['idPanier' => $panier->getId()]);}
            return $this->redirectToRoute('create-checkout-session', ['idPanier' => $panier->getId()]);


        }
        return $this->render('Commande/checkout1.html.twig', [
            'form' => $form->createView(),
        ]);
    }








    #[Route('/liste-commandes', name: 'liste_commandes')]
    public function listeCommandes(CommandeRepository $commandeRepository): Response
    {
        // Récupérer toutes les commandes
        $commandes = $commandeRepository->findAll();

        // Créer un tableau pour stocker les informations sur les commandes de chaque client
        $listeCommandesClients = [];

        // Pour chaque commande
        foreach ($commandes as $commande) {
            // Récupérer le panier associé à cette commande
            $panier = $commande->getPanier();

            // Vérifier si un panier est associé à cette commande
            if ($panier) {
                // Récupérer les lignes de panier associées à ce panier
                $lignesPanier = $panier->getLignepaniers();

                // Créer un tableau pour stocker les informations sur les produits de la commande
                $listeProduitsCommande = [];

                // Pour chaque ligne de panier
                foreach ($lignesPanier as $lignePanier) {
                    // Récupérer le produit associé à la ligne de panier
                    $produit = $lignePanier->getProduit();

                    // Ajouter les informations du produit au tableau
                    $listeProduitsCommande[] = [
                        'nom' => $produit->getNom(),
                        'prix' => $produit->getPrix(),
                        'catégorie' => $produit->getCategorie(),
                    ];
                }

                // Ajouter les informations sur la commande avec la liste des produits au tableau des commandes clients
                $listeCommandesClients[] = [
                    'id' => $panier->getClient()->getId(),
                    'nom' => $panier->getClient()->getNom(),
                    'Montant' => $panier->getPrixTotal(),
                    'adresse' => $commande->getAdress(),
                    'idc' => $commande->getId(),
                    'methodePaiement' => $commande->getMethodePaiement(),
                    'methodeLivraison' => $commande->getMethodeLivraison(),
                    'DateC' => $commande->getDateC(),
                    'statut' => $commande->getStatut(),
                    'phone' => $commande->getPhone(),
                    'produits' => $listeProduitsCommande,
                ];
            }
        }

        // Afficher la vue avec les informations sur les commandes de chaque client
        return $this->render('Commande/Liste_commandes.html.twig', [
            'listeCommandesClients' => $listeCommandesClients,
        ]);
    }



    #[Route('/modifier-statut-commande/{idc}', name: 'modifier_statut_commande')]
    public function modifierStatutCommande($idc,ManagerRegistry $managerRegistry,CommandeRepository $commandeRepository): Response
    {
        $commande=$commandeRepository->find($idc);
        // Modifier le statut de la commande
        $commande->setStatut("Terminée");

        // Enregistrer les modifications dans la base de données
        $entityManager = $managerRegistry->getManager();
        $entityManager->flush();

        // Rediriger l'utilisateur vers une page de confirmation ou vers la liste des commandes
        return $this->redirectToRoute('liste_commandes');
    }

    #[Route('/update/{idp}' , name:'Commande_update')]
    public function update(Request $request,$idp,CommandeRepository $repo,PanierRepository $panierRepository,ManagerRegistry $manager){
        $panier=$panierRepository->find($idp);
        $commande = $repo->findOneBy(['panier' => $panier]);
        $form=$this->createForm(CommandType::class,$commande);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $em=$manager->getManager();
            $em->persist($commande);
            $em->flush();
            return $this->redirectToRoute('showcommande', ['idPanier' => $panier->getId()]);
        }

        return $this->render('Commande/checkout1.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/listcommande', name: 'listcommande')]
    public function getCommandesParPanier(Security $security, CommandeRepository $commandeRepository): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $security->getUser();

        // Vérifier si l'utilisateur est connecté
        if ($user === null) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette fonctionnalité.');
            return $this->redirectToRoute('app_login');
        }

        // Récupérer tous les paniers de l'utilisateur
        $paniers = $user->getPaniers();

        // Initialiser un tableau pour stocker les données des commandes
        $commandesAvecProduits = [];

        // Parcourir chaque panier de l'utilisateur
        foreach ($paniers as $panier) {
            // Récupérer la commande associée à ce panier
            $commande = $commandeRepository->findOneBy(['panier' => $panier]);

            // Si une commande est trouvée
            if ($commande !== null) {
                // Récupérer les produits de cette commande
                $produits = [];
                foreach ($panier->getLignepaniers() as $lignepanier) { // Utilisation de getLignepanier() au lieu de getLignepaniers()
                    // Ajouter le nom du produit à la liste des produits
                    $produits[] = $lignepanier->getProduit();
                }

                // Stocker la commande avec les produits dans le tableau
                $commandesAvecProduits[] = [
                    'commande' => $commande,
                    'produits' => $produits,
                ];
            }
        }

        // Afficher la vue avec les informations sur les commandes de chaque client
        return $this->render('Commande/Commandes_client.html.twig', [
            'commandesAvecProduits' => $commandesAvecProduits,
        ]);
    }

    #[Route('/modifier-statutC/{idc}', name: 'modifier_statutC')]
    public function modifierStatutCommandeC($idc,ManagerRegistry $managerRegistry,CommandeRepository $commandeRepository): Response
    {
        $commande=$commandeRepository->find($idc);
        // Modifier le statut de la commande
        $commande->setStatut("Annulé");

        // Enregistrer les modifications dans la base de données
        $entityManager = $managerRegistry->getManager();
        $entityManager->flush();

        // Rediriger l'utilisateur vers une page de confirmation ou vers la liste des commandes
        return $this->redirectToRoute('listcommande');
    }


}
