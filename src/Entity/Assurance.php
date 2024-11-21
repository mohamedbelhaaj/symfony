<?php

namespace App\Entity;

use App\Repository\AssuranceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: AssuranceRepository::class)]
#[UniqueEntity(
    fields: ['id_vehicule'],
    message: 'Vous ne pouvez pas faire une autre assurance pour le même véhicule.',
)]
#[UniqueEntity(
    fields: ['id_immobilier'],
    message: 'Vous ne pouvez pas faire une autre assurance pour le même immobilier.',
)]
class Assurance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Le champ de type d'assurance est obligatoire")]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_d = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_f = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"Le champ Prix est obligatoire")]
    private ?float $prix = null;
    
    #[ORM\ManyToOne(inversedBy: 'assurances')]
    private ?Utilisateur $id_user = null;

    #[ORM\OneToOne]///(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'id_immobilier', referencedColumnName: 'id_fiscal')]
    private ?Immobilier $id_immobilier = null;

    #[ORM\OneToOne]///(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'id_vehicule', referencedColumnName: 'matricule')]
    private ?Vehicule $id_vehicule = null;



    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->date_d !== null && $this->date_f !== null) {
            if ($this->date_d == $this->date_f) {
                $context->buildViolation("La date de début doit être différente de la date de fin.")
                    ->atPath('date_f')
                    ->addViolation();
            } elseif ($this->date_d >= $this->date_f) {
                $context->buildViolation("La date de début doit être antérieure à la date de fin.")
                    ->atPath('date_f')
                    ->addViolation();
            }
        }
    }

    #[Assert\Callback]
    public function validateAtLeastOneChoice(ExecutionContextInterface $context)
    {
        if (!$this->id_immobilier && !$this->id_vehicule && $this->type != "Assurance Vie" ) {
            $context->buildViolation("Veuillez choisir au moins un Véhicule/Immobilier à assurer.")
                ->atPath('id_immobilier')
                ->addViolation();
        }
    }



    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateD(): ?\DateTimeInterface
    {
        return $this->date_d;
    }

    public function setDateD(\DateTimeInterface $date_d): static
    {
        $this->date_d = $date_d;

        return $this;
    }

    public function getDateF(): ?\DateTimeInterface
    {
        return $this->date_f;
    }

    public function setDateF(\DateTimeInterface $date_f): static
    {
        $this->date_f = $date_f;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

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

    public function getIdImmobilier(): ?Immobilier
    {
        return $this->id_immobilier;
    }

    public function setIdImmobilier(?Immobilier $id_immobilier): static
    {
        $this->id_immobilier = $id_immobilier;

        return $this;
    }

    public function getIdVehicule(): ?Vehicule
    {
        return $this->id_vehicule;
    }

    public function setIdVehicule(?Vehicule $id_vehicule): static
    {
        $this->id_vehicule = $id_vehicule;

        return $this;
    }
}