<?php

namespace App\Entity;

use App\Repository\DevisItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DevisItemRepository::class)]
class DevisItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $designation = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column]
    private ?float $prix_u = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\ManyToOne(inversedBy: 'devisItems')]
    private ?Devis $id_devis = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(?string $designation): static
    {
        $this->designation = $designation;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrixU(): ?float
    {
        return $this->prix_u;
    }

    public function setPrixU(float $prix_u): static
    {
        $this->prix_u = $prix_u;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getIdDevis(): ?Devis
    {
        return $this->id_devis;
    }

    public function setIdDevis(?Devis $id_devis): static
    {
        $this->id_devis = $id_devis;

        return $this;
    }
}