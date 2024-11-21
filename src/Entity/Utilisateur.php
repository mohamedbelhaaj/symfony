<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255,unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $mdp = null;

    #[ORM\Column(length:8)]
    private ?int $tel = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_n = null;

    #[ORM\Column]
    private ?string $role = null;

    #[ORM\OneToMany(mappedBy: 'id_user', targetEntity: Immobilier::class)]
    private Collection $immobiliers;

    #[ORM\OneToMany(mappedBy: 'id_user', targetEntity: Vehicule::class)]
    private Collection $vehicules;

    #[ORM\OneToMany(mappedBy: 'id_user', targetEntity: Publication::class)]
    private Collection $publications;

    #[ORM\OneToMany(mappedBy: 'id_user', targetEntity: Assurance::class)]
    private Collection $assurances;

    #[ORM\OneToMany(mappedBy: 'id_user', targetEntity: Constat::class)]
    private Collection $constats;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Demande::class)]
    private Collection $demandes;

    #[ORM\OneToMany(mappedBy: 'id_user', targetEntity: Devis::class)]
    private Collection $devis;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Like::class)]
    private Collection $likes;

    #[ORM\OneToMany(mappedBy: 'id_user', targetEntity: Commentaire::class)]
    private Collection $commentaires;

    #[ORM\OneToMany(mappedBy: 'id_expert', targetEntity: Evaluation::class)]
    private Collection $evaluations;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_c = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: VerificationToken::class)]
    private Collection $verificationTokens;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ResetPasswordToken::class)]
    private Collection $ResetPasswordTokens;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'User', targetEntity: Reclamation::class)]
    private Collection $reclamations;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: RDV::class)]
    private Collection $rDVs;

    public function __construct()
    {
        $this->immobiliers = new ArrayCollection();
        $this->vehicules = new ArrayCollection();
        $this->publications = new ArrayCollection();
        $this->assurances = new ArrayCollection();
        $this->constats = new ArrayCollection();
        $this->demandes = new ArrayCollection();
        $this->devis = new ArrayCollection();
        $this->evaluations = new ArrayCollection();
        $this->verificationTokens = new ArrayCollection();
        $this->ResetPasswordTokens = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->reclamations = new ArrayCollection();
        $this->rDVs = new ArrayCollection();
    }

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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

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

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): static
    {
        $this->mdp = $mdp;

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

    public function getDateN(): ?\DateTime
    {
        return $this->date_n;
    }

    public function setDateN(\DateTime $date_n): static
    {
        $this->date_n = $date_n;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, Immobilier>
     */
    public function getImmobiliers(): Collection
    {
        return $this->immobiliers;
    }

    public function addImmobilier(Immobilier $immobilier): static
    {
        if (!$this->immobiliers->contains($immobilier)) {
            $this->immobiliers->add($immobilier);
            $immobilier->setIdUser($this);
        }

        return $this;
    }

    public function removeImmobilier(Immobilier $immobilier): static
    {
        if ($this->immobiliers->removeElement($immobilier)) {
            // set the owning side to null (unless already changed)
            if ($immobilier->getIdUser() === $this) {
                $immobilier->setIdUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Vehicule>
     */
    public function getVehicules(): Collection
    {
        return $this->vehicules;
    }

    public function addVehicule(Vehicule $vehicule): static
    {
        if (!$this->vehicules->contains($vehicule)) {
            $this->vehicules->add($vehicule);
            $vehicule->setIdUser($this);
        }

        return $this;
    }

    public function removeVehicule(Vehicule $vehicule): static
    {
        if ($this->vehicules->removeElement($vehicule)) {
            // set the owning side to null (unless already changed)
            if ($vehicule->getIdUser() === $this) {
                $vehicule->setIdUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Publication>
     */
    public function getPublications(): Collection
    {
        return $this->publications;
    }

    public function addPublication(Publication $publication): static
    {
        if (!$this->publications->contains($publication)) {
            $this->publications->add($publication);
            $publication->setIdUser($this);
        }

        return $this;
    }

    public function removePublication(Publication $publication): static
    {
        if ($this->publications->removeElement($publication)) {
            // set the owning side to null (unless already changed)
            if ($publication->getIdUser() === $this) {
                $publication->setIdUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RDV>
     */
    public function getRDV(): Collection
    {
        return $this->rDVs;
    }

    public function addRDV(RDV $rDV): static
    {
        if (!$this->rDVs->contains($rDV)) {
            $this->rDVs->add($rDV);
            $rDV->setUser($this);
        }

        return $this;
    }

    public function removeRDV(RDV $rDV): static
    {
        if ($this->publications->removeElement($rDV)) {
            // set the owning side to null (unless already changed)
            if ($rDV->getUser() === $this) {
                $rDV->setUser(null);
            }
        }

        return $this;
    }
    /**
     * @return Collection<int, Assurance>
     */
    public function getAssurances(): Collection
    {
        return $this->assurances;
    }

    public function addAssurance(Assurance $assurance): static
    {
        if (!$this->assurances->contains($assurance)) {
            $this->assurances->add($assurance);
            $assurance->setIdUser($this);
        }

        return $this;
    }

    public function removeAssurance(Assurance $assurance): static
    {
        if ($this->assurances->removeElement($assurance)) {
            // set the owning side to null (unless already changed)
            if ($assurance->getIdUser() === $this) {
                $assurance->setIdUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Constat>
     */
    public function getConstats(): Collection
    {
        return $this->constats;
    }

    public function addConstat(Constat $constat): static
    {
        if (!$this->constats->contains($constat)) {
            $this->constats->add($constat);
            $constat->setIdUser($this);
        }

        return $this;
    }

    public function removeConstat(Constat $constat): static
    {
        if ($this->constats->removeElement($constat)) {
            // set the owning side to null (unless already changed)
            if ($constat->getIdUser() === $this) {
                $constat->setIdUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Demande>
     */
    public function getDemandes(): Collection
    {
        return $this->demandes;
    }

    public function addDemande(Demande $demande): static
    {
        if (!$this->demandes->contains($demande)) {
            $this->demandes->add($demande);
            $demande->setUser($this);
        }

        return $this;
    }

    public function removeDemande(Demande $demande): static
    {
        if ($this->demandes->removeElement($demande)) {
            // set the owning side to null (unless already changed)
            if ($demande->getUser() === $this) {
                $demande->setUser(null);
            }
        }

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
            $devi->setIdUser($this);
        }

        return $this;
    }

    public function removeDevi(Devis $devi): static
    {
        if ($this->devis->removeElement($devi)) {
            // set the owning side to null (unless already changed)
            if ($devi->getIdUser() === $this) {
                $devi->setIdUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setUser($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getUser() === $this) {
                $like->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setIdUser($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getIdUser() === $this) {
                $commentaire->setIdUser(null);
            }
        }

        return $this;
    }

    public function getDateC(): ?\DateTime
    {
        return $this->date_c;
    }

    public function setDateC(\DateTime $date_c): static
    {
        $this->date_c = $date_c;

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

    /**
     * @return Collection<int, VerificationToken>
     */
    public function getVerificationTokens(): Collection
    {
        return $this->verificationTokens;
    }

    public function addVerificationToken(VerificationToken $verificationToken): static
    {
        if (!$this->verificationTokens->contains($verificationToken)) {
            $this->verificationTokens->add($verificationToken);
            $verificationToken->setUser($this);
        }

        return $this;
    }

    public function removeVerificationToken(VerificationToken $verificationToken): static
    {
        if ($this->verificationTokens->removeElement($verificationToken)) {
            // set the owning side to null (unless already changed)
            if ($verificationToken->getUser() === $this) {
                $verificationToken->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ResetPasswordToken>
     */
    public function getResetPasswordTokens(): Collection
    {
        return $this->ResetPasswordTokens;
    }

    public function addResetPasswordToken(ResetPasswordToken $ResetPasswordToken): static
    {
        if (!$this->ResetPasswordTokens->contains($ResetPasswordToken)) {
            $this->ResetPasswordTokens->add($ResetPasswordToken);
            $ResetPasswordToken->setUser($this);
        }

        return $this;
    }

    public function removeResetPasswordToken(ResetPasswordToken $ResetPasswordToken): static
    {
        if ($this->ResetPasswordTokens->removeElement($ResetPasswordToken)) {
            // set the owning side to null (unless already changed)
            if ($ResetPasswordToken->getUser() === $this) {
                $ResetPasswordToken->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Evaluation>
     */
    public function getEvaluations(): Collection
    {
        return $this->evaluations;
    }

    public function addEvaluation(Evaluation $evaluation): static
    {
        if (!$this->evaluations->contains($evaluation)) {
            $this->evaluations->add($evaluation);
            $evaluation->setIdExpert($this);
        }

        return $this;
    }

    public function removeEvaluation(Evaluation $evaluation): static
    {
        if ($this->evaluations->removeElement($evaluation)) {
            // set the owning side to null (unless already changed)
            if ($evaluation->getIdExpert() === $this) {
                $evaluation->setIdExpert(null);
            }
        }

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getUsername(): ?string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->mdp;
    }

    public function getRoles(): array
    {
    $roles = [$this->role ?? 'ROLE_USER']; // Default role if not set

    // Map your custom roles to Symfony roles
    switch ($this->role) {
        case 'Admin':
            $roles[] = 'ROLE_ADMIN';
            break;
        case 'Client':
            $roles[] = 'ROLE_CLIENT';
            break;
        case 'Agent SOS':
            $roles[] = 'ROLE_AGENT_SOS';
            break;
        case 'Expert':
            $roles[] = 'ROLE_EXPERT';
            break;
        default:
            break;
    }

    return $roles;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isAdmin(): bool
    {
        // Check if the user has the 'Admin' role
        return \in_array('ROLE_ADMIN', $this->getRoles(), true);
    }

    public function isExpert(): bool
    {
        // Check if the user has the 'Expert' role
        return \in_array('ROLE_EXPERT', $this->getRoles(), true);
    }

    public function isSOS(): bool
    {
        // Check if the user has the 'Agent_SOS' role
        return \in_array('ROLE_AGENT_SOS', $this->getRoles(), true);
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Reclamation>
     */
    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    public function addReclamation(Reclamation $reclamation): static
    {
        if (!$this->reclamations->contains($reclamation)) {
            $this->reclamations->add($reclamation);
            $reclamation->setUser($this);
        }

        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): static
    {
        if ($this->reclamations->removeElement($reclamation)) {
            // set the owning side to null (unless already changed)
            if ($reclamation->getUser() === $this) {
                $reclamation->setUser(null);
            }
        }

        return $this;
    }
    
}
