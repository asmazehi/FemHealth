<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 */
class Commande
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank(message: "l'adresse ne doit pas être vide.")]
    private $adress;

    /**
     * @ORM\Column(type="date")
     */
    private $DateC;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $statut;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank(message: "la méthode de paiement ne doit pas être vide.")]
    private $Methode_paiement;

    /**
     * @ORM\OneToOne(targetEntity=Panier::class, cascade={"persist", "remove"})
     */
    private $panier;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le numéro de téléphone ne peut pas être vide")
     * @Assert\Length(
     *      min = 8,
     *      max = 8,
     *      exactMessage = "Le numéro de téléphone doit avoir exactement {{ limit }} chiffres"
     * )
     * @Assert\Regex(
     *      pattern="/^[2-9][0-9]{7}$/",
     *      message="Le numéro de téléphone doit commencer par un chiffre entre 2 et 9 et avoir 8 chiffres au total"
     * )
     */

    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank(message: "la methode de livraison ne doit pas être vide.")]
    private $methode_livraison;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getDateC(): ?\DateTimeInterface
    {
        return $this->DateC;
    }

    public function setDateC(\DateTimeInterface $DateC): self
    {
        $this->DateC = $DateC;

        return $this;
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

    public function getMethodePaiement(): ?string
    {
        return $this->Methode_paiement;
    }

    public function setMethodePaiement(string $Methode_paiement): self
    {
        $this->Methode_paiement = $Methode_paiement;

        return $this;
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(?Panier $panier): self
    {
        $this->panier = $panier;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getMethodeLivraison(): ?string
    {
        return $this->methode_livraison;
    }

    public function setMethodeLivraison(string $methode_livraison): self
    {
        $this->methode_livraison = $methode_livraison;

        return $this;
    }




}
