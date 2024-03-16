<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Panier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class StripeController extends AbstractController
{
    #[Route('/stripe', name: 'app_stripe')]
    public function index(): Response
    {
        return $this->render('stripe/index.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }
    #[Route('/create-checkout-session/{idPanier}', name: 'create-checkout-session')]
    public function paiement(EntityManagerInterface $entityManager, $idPanier, Request $request,UrlGeneratorInterface $urlGenerator): Response
    {
        // Récupérer le panier depuis la base de données en utilisant l'EntityManager
        $panier = $entityManager->getRepository(Panier::class)->find($idPanier);

        // Vérifier si le panier existe
        if (!$panier) {
            throw $this->createNotFoundException('Aucun panier trouvé pour cet ID: '.$idPanier);
        }

        // Récupérer les lignes de panier et les produits associés
        $lignesPanier = $panier->getLignepaniers();
        $produits = [];
        foreach ($lignesPanier as $lignePanier) {
            $produits[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $lignePanier->getProduit()->getNom(), // Utilisez le nom du produit de la ligne de panier
                    ],
                    'unit_amount' => $lignePanier->getProduit()->getPrix() * 100, // Convertissez le prix en centimes
                ],
                'quantity' => $lignePanier->getQuantite(), // Quantité de chaque produit dans le panier
            ];
        }

        // Créer une session de paiement Stripe
        $stripe = new \Stripe\StripeClient('sk_test_51Op589Hvqq7mfMH0fdKOSMMO2pf9QxdTW3Q6pBG13IVODxd9uudifpaL9KS2NgJEG5DyVC7nLFr3XYe5QRmiQa0C009i4gvPkC');

        $checkout_session = $stripe->checkout->sessions->create([
            'payment_method_types' => ['card'], // Ajoutez d'autres méthodes de paiement si nécessaire
            'line_items' => $produits, // Utilisez les produits récupérés à partir du panier
            'mode' => 'payment',
            'success_url' => $urlGenerator->generate('showcommande', ['idPanier' => $idPanier], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $urlGenerator->generate('cancelcommande', ['idPanier' => $idPanier], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
// Redirigez l'utilisateur vers l'URL de paiement Stripe
        return new RedirectResponse($checkout_session->url);
    }

    #[Route('/cancel-commande/{idPanier}', name: 'cancelcommande')]
    public function annulerCommande(EntityManagerInterface $entityManager, $idPanier): Response
    {
        // Récupérer la commande associée au panier
        $commande = $entityManager->getRepository(Commande::class)->findOneBy(['panier' => $idPanier]);

        // Vérifier si une commande a été trouvée
        if (!$commande) {
            throw $this->createNotFoundException('Aucune commande trouvée pour ce panier');
        }

        // Changer le statut de la commande en "annulée"
        $commande->setStatut('annulée');

        // Mettre à jour la commande dans la base de données
        $entityManager->flush();

        // Rediriger vers la page listcommande
        return $this->redirectToRoute('listcommande');
    }

}
