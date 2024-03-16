<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProduitRepository::class)
 */
class Produit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

     /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le nom est requis")
     */
    private $Nom;

    /**
     * @ORM\Column(type="float"))
     * @Assert\NotBlank(message="Le prix est requis")
     * @Assert\Type(type="numeric", message="Le prix doit être un nombre")
     * @Assert\PositiveOrZero(message="Le prix doit être positif ou zéro")
     */
    private $Prix;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="Le taux de remise est requis")
     * @Assert\Type(type="numeric", message="Le taux de remise doit être un nombre")
     * @Assert\Range(min=0, max=100, notInRangeMessage="Le taux de remise doit être compris entre 0 et 100")
     */
    private $Taux_remise;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="La catégorie est requise")
     */
    private $Categorie;

    /**
     * @ORM\Column(type="string", length=255)*/
     
    private $image;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="La description est requise")
     */
    private $Description;

    /**
     * @ORM\ManyToOne(targetEntity=Sponsor::class, inversedBy="produits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sponsor;

    /**
     * @ORM\PreRemove
     */
    public function removeImage(string $imageDirectory): void
    {   
            $filename = $this->image;
            if ($filename) {
                $filePath = $imageDirectory . $filename;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }   
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

    public function getPrix(): ?int
    {
        return $this->Prix;
    }

    public function setPrix(int $Prix): self
    {
        $this->Prix = $Prix;

        return $this;
    }

    public function getTauxRemise(): ?int
    {
        return $this->Taux_remise;
    }

    public function setTauxRemise(int $Taux_remise): self
    {
        $this->Taux_remise = $Taux_remise;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->Categorie;
    }

    public function setCategorie(string $Categorie): self
    {
        $this->Categorie = $Categorie;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    public function getSponsor(): ?Sponsor
    {
        return $this->sponsor;
    }

    public function setSponsor(?Sponsor $sponsor): self
    {
        $this->sponsor = $sponsor;

        return $this;
    }

   
}
