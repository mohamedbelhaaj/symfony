<?php

namespace App\Entity;

use App\Repository\ImmobilierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImmobilierRepository::class)]
class Immobilier
{
    #[ORM\Id]
    #[ORM\Column]
    private ?int $id_fiscal = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column]
    private ?int $superficie = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'immobiliers')]
    private ?Utilisateur $id_user = null;

    #[ORM\Column(length: 255)]
    private ?string $titre_prop = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Evaluation $evaluation = null;

    public function getIdfiscal(): ?int
    {
        return $this->id_fiscal;
    }

    public function setIdfiscal(string $id_fiscal): static
    {
        $this->id_fiscal = $id_fiscal;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getSuperficie(): ?int
    {
        return $this->superficie;
    }

    public function setSuperficie(int $superficie): static
    {
        $this->superficie = $superficie;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getIdUser(): ?Utilisateur
    {
        return $this->id_user;
    }

    public function setIdUser(?Utilisateur $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getTitreProp(): ?string
    {
        return $this->titre_prop;
    }

    public function setTitreProp(string $titre_prop): static
    {
        $this->titre_prop = $titre_prop;

        return $this;
    }

    public function getEvaluation(): ?Evaluation
    {
        return $this->evaluation;
    }

    public function setEvaluation(?Evaluation $evaluation): static
    {
        $this->evaluation = $evaluation;

        return $this;
    }
}