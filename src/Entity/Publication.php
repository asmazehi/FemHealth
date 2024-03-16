<?php

namespace App\Entity;

use App\Repository\PublicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PublicationRepository::class)
 */
class Publication
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
    #[Assert\NotBlank(message: "Veuillez saisir le contenu.")]
    #[Assert\Length(max:140, maxMessage:"Le contenu ne doit pas dépasser {{ limit }} caractères.")]
    private $contenu;

    /**
     * @ORM\Column(type="string", length=255)
     */
    
    private     $image;
    
    /**
     * @ORM\OneToMany(targetEntity=Commentaire::class, mappedBy="publication", orphanRemoval=true)
     */
    private      $commentaires;
    /**
      * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $datepub;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->datepub = new \DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image): self
    {
        $this->image = $image;

        return $this;
    }
    public function getDatepub(): ?\DateTimeInterface
{
    return $this->datepub;
}

public function setDatepub(\DateTimeInterface $datepub): self
{
    $this->datepub = $datepub;

    return $this;
}

public function getTitre(): ?string
{
    return $this->titre;
}

public function setTitre(string $titre): self
{
    $this->titre = $titre;

    return $this;
}

    
    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setPublication($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getPublication() === $this) {
                $commentaire->setPublication(null);
            }
        }

        return $this;
    }
    
}
