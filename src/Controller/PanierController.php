<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\User;
use App\Entity\LignePanier;
use App\Repository\CommandeRepository;
use App\Controller\ProduitController;

use App\Repository\PanierRepository;
use App\Repository\LignepanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class PanierController extends AbstractController
{
    #[Route('/panier' , name: 'panier')]
    public function index(): Response
    {
        return $this->render('panier/index.html.twig', [
            'controller_name' => 'PanierController',
        ]);
    }

    #[Route('/panier/delete/{idProduit}/{idPanier}', name: 'app_delete')]
    public function deleteProduct(EntityManagerInterface $entityManager, $idProduit, $idPanier, LignePanierRepository $lignePanierRepository)
    {

        $lignePanier = $lignePanierRepository
            ->findOneBy(['panier' => $idPanier, 'produit' => $idProduit]);

        if (!$lignePanier) {
            throw $this->createNotFoundException('Ligne de panier non trouvée avec l\'identifiant ');
        }

        // Récupérez le panier associé à la ligne de panier
        $panier = $lignePanier->getPanier();

        // Supprimez la ligne de panier de la collection du panier
        $panier->getLignepaniers()->removeElement($lignePanier);

        // Supprimez l'entité de la base de données
        $entityManager->remove($lignePanier);
        $entityManager->flush();

        // Redirigez vers la page de liste des produits dans le panier
        return $this->redirectToRoute('liste_produits_par_panier', ['idPanier' => $panier->getId()]);
    }

    #[Route('/panierSH', name: 'liste_produits_par_panier')]
    public function listeProduitsParPanier(Security $security, CommandeRepository $CommandeRepository,EntityManagerInterface $entityManager): Response
    {
        $user = $security->getUser();
        $panier = $user->getPanierActif($CommandeRepository);



        if (!$panier) {
            throw $this->createNotFoundException(
                'Aucun panier trouvé  '
            );

        }
        $idPanier=$panier->getId();
        $lignesPanier = $panier->getLignepaniers();
        $produits = [];
        foreach ($lignesPanier as $lignePanier) {
            $produits[] = $lignePanier->getProduit();
        }
        return $this->render('Panier/ShowPanier.html.twig', [
            'produits' => $produits,
            'idPanier' => $idPanier
        ]);
    }

    #[Route('/panieradd/{id}', name: 'add-produit')]
    public function addpanier(Security $security, EntityManagerInterface $entityManager, PanierRepository $panierRepository, $id, CommandeRepository $CommandeRepository): Response
    {
        // Récupérez l'utilisateur actuel
        $user = $security->getUser();

        // Vérifiez si l'utilisateur est connecté
        if ($user === null) {
            // Utilisateur non connecté, affichez un message d'erreur ou redirigez vers une autre page
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette fonctionnalité.');
            return $this->redirectToRoute('app_login');
        } else {
            // Récupérer le panier actif du client
            $panier = $user->getPanierActif($CommandeRepository);

            // Si le panier actif n'existe pas ou n'est pas actif, créez un nouveau panier
            if ($panier === null) {
                $panier = new Panier();
                $panier->setClient($user);
                $panier->setStatut("En Cour");
                $panier->setPrixTotal(0);
                $entityManager->persist($panier);
            }

            // Trouver le produit à ajouter au panier
            $produit = $entityManager->getRepository(Produit::class)->find($id);
            if (!$produit) {
                throw $this->createNotFoundException('Produit non trouvé');
            }

            $lignePanier = $panier->getLignepaniers()->filter(function ($ligne) use ($produit) {
                return $ligne->getProduit() === $produit;
            })->first();

            // Si le produit existe déjà dans le panier, incrémentez sa quantité
            if ($lignePanier) {
                $lignePanier->setQuantite($lignePanier->getQuantite() + 1); // Mettre à jour la quantité existante
            } else {
                // Créer une nouvelle ligne de panier pour le produit
                $lignePanier = new LignePanier();
                $lignePanier->setPanier($panier);
                $lignePanier->setProduit($produit);
                $lignePanier->setQuantite(1);
                $entityManager->persist($lignePanier);

                // Mettre à jour le prix total du panier
                $panier->setPrixTotal($panier->getPrixTotal() + $produit->getPrix() * $lignePanier->getQuantite());

                // Enregistrer les modifications dans la base de données
                $entityManager->flush();


            }
        }
        // Rediriger vers la page du panier
        return $this->redirectToRoute('app_produit_indexFront');
    }

    // Fonction pour calculer le total du panier
    private function calculateTotalPanier(Panier $panier): float
    {
        $total = 0;
        foreach ($panier->getLignepaniers() as $lignePanier) {
            $total += $lignePanier->getProduit()->getPrix() * $lignePanier->getQuantite();
        }
        return $total;
    }

    #[Route('/quantiteprod/{idPanier}', name: 'quantiteprod')]
    public function quantiteprod(Request $request, $idPanier, EntityManagerInterface $entityManager, PanierRepository $panierRepository, CommandeRepository $CommandeRepository): Response
    {
        // Récupérer le panier
        $panier = $panierRepository->find($idPanier);

        // Vérifier si le panier existe
        if (!$panier) {
            throw $this->createNotFoundException('Panier non trouvé');
        }

        // Récupérer les quantités envoyées via le formulaire
        $quantites = [];
        foreach ($request->request->all() as $key => $value) {
            if (strpos($key, 'quantite_') === 0) {
                $produitId = substr($key, strlen('quantite_'));
                $quantites[$produitId] = $value;
            }
        }

        // Mettre à jour les quantités des produits dans le panier
        foreach ($panier->getLignepaniers() as $lignePanier) {
            $produitId = $lignePanier->getProduit()->getId();
            if (isset($quantites[$produitId])) {
                $nouvelleQuantite = $quantites[$produitId];
                $lignePanier->setQuantite($nouvelleQuantite);
            }
        }

        // Mettre à jour le prix total du panier
        $panier->setPrixTotal($this->calculateTotalPanier($panier));

        // Enregistrer les modifications dans la base de données
        $entityManager->flush();

        // Redirection vers la page Command_add
        return $this->redirectToRoute('Command_add', ['idPanier' => $panier->getId()]);
    }
}
