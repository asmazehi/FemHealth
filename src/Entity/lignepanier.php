<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 */
class lignepanier{ /**
 * @ORM\Id()
 * @ORM\GeneratedValue()
 * @ORM\Column(type="integer")
 */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Panier")
     * @ORM\JoinColumn(nullable=false)
     */
    private $panier;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Produit")
     * @ORM\JoinColumn(nullable=false)
     */
    private $produit;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantite;

    /**
     * @return int|null
     */
    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }
    public function setProduit(?Produit $produit): void
    {
        $this->produit = $produit;
    }

    public function setQuantite(?int $quantite): void
    {
        $this->quantite = $quantite;
    }


    // Getter pour la relation $panier
    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

// Setter pour la relation $panier
    public function setPanier(?Panier $panier): void
    {
        $this->panier = $panier;
    }

}
