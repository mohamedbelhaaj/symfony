<?php

namespace App\Entity;

use App\Repository\DevisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DevisRepository::class)]
class Devis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    private ?float $totalTTC = null;

    #[ORM\Column(length: 255)]
    private ?string $etat = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $remarque = null;

    #[ORM\ManyToOne(inversedBy: 'devis')]
    private ?Utilisateur $id_user = null;

    #[ORM\OneToMany(mappedBy: 'id_devis', targetEntity: DevisItem::class)]
    private Collection $devisItems;

    #[ORM\ManyToOne(targetEntity:Vehicule::class, inversedBy:"devis")]
    #[ORM\JoinColumn(name:"vehicule_matricule", referencedColumnName:"matricule", nullable:false)]
    private ?Vehicule $vehicule = null;

    public function __construct()
    {
        $this->devisItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getTotalTTC(): ?float
    {
        return $this->totalTTC;
    }

    public function setTotalTTC(float $totalTTC): static
    {
        $this->totalTTC = $totalTTC;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getRemarque(): ?string
    {
        return $this->remarque;
    }

    public function setRemarque(?string $remarque): static
    {
        $this->remarque = $remarque;

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
     * @return Collection<int, DevisItem>
     */
    public function getDevisItems(): Collection
    {
        return $this->devisItems;
    }

    public function addDevisItem(DevisItem $devisItem): static
    {
        if (!$this->devisItems->contains($devisItem)) {
            $this->devisItems->add($devisItem);
            $devisItem->setIdDevis($this);
        }

        return $this;
    }

    public function removeDevisItem(DevisItem $devisItem): static
    {
        if ($this->devisItems->removeElement($devisItem)) {
            // set the owning side to null (unless already changed)
            if ($devisItem->getIdDevis() === $this) {
                $devisItem->setIdDevis(null);
            }
        }

        return $this;
    }

    public function getVehicule(): ?Vehicule
    {
        return $this->vehicule;
    }

    public function setVehicule(?Vehicule $vehicule): static
    {
        $this->vehicule = $vehicule;

        return $this;
    }

}