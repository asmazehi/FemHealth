<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Publication;
use Symfony\Component\Security\Core\User\UserInterface;
/**
 * @ORM\Entity(repositoryClass=CommentaireRepository::class)
 */
class Commentaire
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
    #[Assert\NotBlank(message: "Veuillez saisir le description.")]
    #[Assert\Length(max:100, maxMessage:"Le description ne doit pas dépasser {{ limit }} caractères.")]
    private $description;

    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $datecomnt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

   /**
 * @ORM\Column(name="likes", type="integer", options={"default": 0})
 */
private $likes;



    /**
     * @ORM\ManyToOne(targetEntity=Publication::class, inversedBy="commentaires")
     * @ORM\JoinColumn(nullable=false)
     */
    private $publication;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getdatecomnt(): ?\DateTimeInterface
    {
        return $this->datecomnt;
    }

    public function setdatecomnt(\DateTimeInterface $datecomnt): self
    {
        $this->datecomnt = $datecomnt;

        return $this;
    }

    public function __construct()
    {
        // Par défaut, un nouveau commentaire est actif
        $this->active = true;
        $this->likes = 0; // Par défaut, aucun like
        $this->datecomnt = new \DateTime('now');
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getLikes(): ?int
    {
        return $this->likes;
    }
    
    public function setLikes(int $likes): self
    {
        $this->likes = $likes;
        return $this;
    }
    

    public function getPublication(): ?Publication
    {
        return $this->publication;
    }

    public function setPublication(?Publication $publication): self
    {
        $this->publication = $publication;

        return $this;
    }
    /**
     * Obtenez l'utilisateur associé à ce commentaire.
     *
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    /**
     * Définir l'utilisateur associé à ce commentaire.
     *
     * @param UserInterface|null $user
     * @return self
     */
    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }
    public function __toString():string{
        return $this->description;
    }
    
    
}
