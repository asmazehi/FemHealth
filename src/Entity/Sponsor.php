<?php

namespace App\Entity;

use App\Repository\SponsorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use PhpParser\Node\Expr\Cast\String_;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SponsorRepository::class)
 */
class Sponsor
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le nom ne peut pas être vide")
     */
    private $Nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="La durée du contrat ne peut pas être vide")
     * @Assert\Regex(
     *     pattern="/^\d{2}\s*mois$/",
     *     message="La durée du contrat doit être sous la forme 'XX mois' où XX est un nombre de 2 chiffres."
     * )
     */
    private $Duree_contrat;

    /**
     * @ORM\OneToMany(targetEntity=Produit::class, mappedBy="sponsor", orphanRemoval=true)
     */
    private $produits;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): self
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getDureeContrat(): ?String
    {
        return $this->Duree_contrat;
    }

    public function setDureeContrat(string $Duree_contrat): self
    {
        $this->Duree_contrat = $Duree_contrat;

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): self
    {
        if (!$this->produits->contains($produit)) {
            $this->produits[] = $produit;
            $produit->setSponsor($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): self
    {
        if ($this->produits->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getSponsor() === $this) {
                $produit->setSponsor(null);
            }
        }

        return $this;
    }
    public function __toString(): string
{
    return $this->Nom;
}

    
}
