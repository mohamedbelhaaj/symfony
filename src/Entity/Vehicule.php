<?php

namespace App\Entity;

use App\Repository\VehiculeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VehiculeRepository::class)]
class Vehicule
{
    #[ORM\Id]
    #[ORM\Column]
    private ?int $matricule = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $modele = null;

    #[ORM\Column(nullable: true)]
    private ?float $prix = null;

    #[ORM\Column]
    private ?string $carte_grise = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'vehicules')]
    private ?Utilisateur $id_user = null;

    #[ORM\OneToMany(mappedBy: 'vehicule', targetEntity: Devis::class)]
    private Collection $devis;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Evaluation $evaluation = null;

    #[ORM\Column(nullable: true)]
    private ?float $score = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $etiquette = null;

    public function __construct()
    {
        $this->devis = new ArrayCollection();
    }

    public function getMatricule(): ?int
    {
        return $this->matricule;
    }

    public function setMatricule(string $matricule): static
    {
        $this->matricule = $matricule;

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

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(string $modele): static
    {
        $this->modele = $modele;

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

    public function getCarteGrise() : ?string
    {
        return $this->carte_grise;
    }

    public function setCarteGrise(string $carte_grise): static
    {
        $this->carte_grise = $carte_grise;

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

    /**
     * @return Collection<int, Devis>
     */
    public function getDevis(): Collection
    {
        return $this->devis;
    }

    public function addDevi(Devis $devi): static
    {
        if (!$this->devis->contains($devi)) {
            $this->devis->add($devi);
            $devi->setVehicule($this);
        }

        return $this;
    }

    public function removeDevi(Devis $devi): static
    {
        if ($this->devis->removeElement($devi)) {
            // set the owning side to null (unless already changed)
            if ($devi->getVehicule() === $this) {
                $devi->setVehicule(null);
            }
        }

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

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(?float $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getEtiquette(): ?string
    {
        return $this->etiquette;
    }

    public function setEtiquette(?string $etiquette): static
    {
        $this->etiquette = $etiquette;

        return $this;
    }
}