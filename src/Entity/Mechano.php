<?php

namespace App\Entity;

use App\Repository\MechanoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MechanoRepository::class)]
class Mechano
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Nom est obligatoire")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Lieu est obligatoire")]
    private ?string $Lieu = null;

    #[ORM\Column]
    #[Assert\Regex(pattern: "/^\d{8}$/", message: "Le téléphone doit être composé par 8 numéros.")]
    #[Assert\NotBlank(message:"Téléphone est obligatoire")]
    private ?int $tel = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Spécialité est obligatoire")]
    private ?string $specialite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->Lieu;
    }

    public function setLieu(string $Lieu): static
    {
        $this->Lieu = $Lieu;

        return $this;
    }

    public function getTel(): ?int
    {
        return $this->tel;
    }

    public function setTel(int $tel): static
    {
        $this->tel = $tel;

        return $this;
    }

    public function getSpecialite(): ?string
    {
        return $this->specialite;
    }

    public function setSpecialite(string $specialite): static
    {
        $this->specialite = $specialite;

        return $this;
    }
}