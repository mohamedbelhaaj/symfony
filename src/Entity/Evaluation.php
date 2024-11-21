<?php

namespace App\Entity;

use App\Repository\EvaluationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvaluationRepository::class)]
class Evaluation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date = null;

    #[ORM\Column]
    private ?float $valeurneuf = null;

    #[ORM\Column]
    private ?float $valeurvenal = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observation = null;

    #[ORM\ManyToOne(inversedBy: 'evaluations')]
    private ?Utilisateur $id_expert = null;

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getValeurneuf(): ?float
    {
        return $this->valeurneuf;
    }

    public function setValeurneuf(float $valeurneuf): static
    {
        $this->valeurneuf = $valeurneuf;

        return $this;
    }

    public function getValeurvenal(): ?float
    {
        return $this->valeurvenal;
    }

    public function setValeurvenal(float $valeurvenal): static
    {
        $this->valeurvenal = $valeurvenal;

        return $this;
    }

    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function setObservation(?string $observation): static
    {
        $this->observation = $observation;

        return $this;
    }

    public function getIdExpert(): ?Utilisateur
    {
        return $this->id_expert;
    }

    public function setIdExpert(?Utilisateur $id_expert): static
    {
        $this->id_expert = $id_expert;

        return $this;
    }

   
}