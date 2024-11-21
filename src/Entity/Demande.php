<?php

// src/Entity/Demande.php

namespace App\Entity;

use App\Repository\DemandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity(repositoryClass: DemandeRepository::class)]
class Demande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $localisation = null;

    #[ORM\Column]
    #[Assert\GreaterThan(value: 0, message: "Veuillez saisir un nombre de personnes supérieur à zéro")]
    #[Assert\LessThanOrEqual(value:  5,message:  "Le nombre de personnes ne peut pas dépasser 5")]
    #[Assert\NotBlank(message:"Nombre de personnes est obligatoire")]
    private ?int $nbr_personnes = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Rating $rating = null;

    #[ORM\Column]
    private ?bool $showagain = true;

    #[ORM\ManyToOne(inversedBy: 'demandes')]
    private ?Utilisateur $user = null;

    #[ORM\ManyToOne(inversedBy: 'demandes')]
    private ?Utilisateur $agent = null;

    #[ORM\Column(nullable: true)]
    private ?int $TempsEstime = null;

    // Getters and setters...

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNbrPersonnes(): ?int
    {
        return $this->nbr_personnes;
    }

    public function setNbrPersonnes(int $nbr_personnes): self
    {
        $this->nbr_personnes = $nbr_personnes;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getRating(): ?Rating
    {
        return $this->rating;
    }

    public function setRating(?Rating $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function isShowagain(): ?bool
    {
        return $this->showagain;
    }

    public function setShowagain(bool $showagain): static
    {
        $this->showagain = $showagain;

        return $this;
    }

    public function getUser(): ?Utilisateur
    {
        return $this->user;
    }

    public function setUser(?Utilisateur $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getAgent(): ?Utilisateur
    {
        return $this->agent;
    }

    public function setAgent(?Utilisateur $agent): static
    {
        $this->agent = $agent;

        return $this;
    }

    public function getTempsEstime(): ?int
    {
        return $this->TempsEstime;
    }

    public function setTempsEstime(?int $TempsEstime): static
    {
        $this->TempsEstime = $TempsEstime;

        return $this;
    }

    
}