<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EvenementRepository::class)
 */
class Evenement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *@Assert\Length(min = 3, minMessage = "Le nom de l'événement doit contenir au moins 3 caractères.")
    
     
     */
    private $nom;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank(message="La date de début est requise")
     * @Assert\GreaterThanOrEqual("today", message="La date de début doit être aujourd'hui ou ultérieure")
     */
    private $date_debut;

  /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank(message="La date de fin est requise")
     * @Assert\GreaterThanOrEqual("today", message="La date de fin doit être après aujourd'hui")
     */
    private $date_fin;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="La localisation est requise")
     * @Assert\Type("string", message="La localisation doit être une chaîne de caractères")
     */
    private $localisation;

    /**
     * @ORM\Column(type="float")
     */
    private $montant;

    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="id_evenement", orphanRemoval=true)
     */
    private $reservations;

    /**
     * @ORM\ManyToOne(targetEntity=Type::class, inversedBy="evenements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;



    /**
     * @ORM\Column(type="integer" , nullable=true)
     */
    private $signaler;



    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;

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

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): self
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }


    public function getSignaler(): ?int
    {
        return $this->signaler;
    }

    public function setSignaler(int $signaler): static
    {
        $this->signaler = $signaler;

        return $this;

    }

    public function incrementSignaler(): void
    {
        $this->signaler = $this->signaler ? $this->signaler + 1 : 1;
    }
    /**
     * @return Collection<int, reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setIdEvenement($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getIdEvenement() === $this) {
                $reservation->setIdEvenement(null);
            }
        }

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function __toString(): string
    {
        return $this->nom;
    }


}
