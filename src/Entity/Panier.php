<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PanierRepository::class)
 */
class Panier
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="integer")
     */


    /**
     * @ORM\Column(type="float")
     */
    private $prix_Total;




    /**
     * @ORM\OneToMany(targetEntity="App\Entity\lignepanier", mappedBy="panier", cascade={"persist", "remove"})
     */
    private $lignepanier;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $statut;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="paniers")
     */
    private $client;

    public function __construct()
    {
        $this->lignepaniers = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }











    /**
     * @return ArrayCollection|LignePanier[]
     */
    public function getLignepaniers(): ArrayCollection
    {
        // Vérifiez si $this->lignepanier est null
        if ($this->lignepanier === null) {
            // Si c'est le cas, retournez une ArrayCollection vide
            return new ArrayCollection();
        }

        // Convertir la PersistentCollection en ArrayCollection
        return new ArrayCollection($this->lignepanier->toArray());
    }


    public function setlignepaniers(ArrayCollection $lignepaniers): void
    {
        $this->lignepanier =$lignepaniers;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }
    public function getPrixTotal(): ?float
    {
        return $this->prix_Total;
    }

    public function setPrixTotal(float $prix_Total): self
    {
        $this->prix_Total = $prix_Total;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->getId(); // Ou toute autre propriété que vous souhaitez afficher comme chaîne de caractères
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): self
    {
        $this->client = $client;

        return $this;
    }
    /**
     * Vérifie si le panier est actif en vérifiant s'il n'existe pas de commande associée.
     *
     * @param CommandeRepository $commandeRepository
     * @return bool Vrai si le panier est actif, faux sinon
     */
    public function estActif(CommandeRepository $commandeRepository): bool
    {
        // Récupérer les commandes associées à ce panier
        $commandes = $commandeRepository->findBy(['panier' => $this]);

        // Si aucune commande n'est trouvée, le panier est considéré comme actif
        return empty($commandes);
    }



    public function addLignepanier(LignePanier $lignePanier): self
    {
        if (!$this->lignepaniers->contains($lignePanier)) {
            $this->lignepaniers[] = $lignePanier;
            $lignePanier->setPanier($this);
        }

        return $this;
    }
}
