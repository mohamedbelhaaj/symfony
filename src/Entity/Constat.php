<?php

namespace App\Entity;

use App\Repository\ConstatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ConstatRepository::class)]
class Constat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"Ce champ est obligatoire.")]
    private ?int $veha_permis = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champ est obligatoire.")]
    private ?string $veha_venant = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champ est obligatoire.")]
    private ?string $veha_allant = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $veha_datepermis = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champ est obligatoire.")]
    private ?string $vehb_assurance = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champ est obligatoire.")]
    private ?string $vehb_police = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champ est obligatoire.")]
    private ?string $vehb_agence = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $vehb_attestationVDD = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $vehb_attestationVDF = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champ est obligatoire.")]
    private ?string $vehb_c_nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champ est obligatoire.")]
    private ?string $vehb_c_prenom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champ est obligatoire.")]
    private ?string $vehb_c_adresse = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"Ce champ est obligatoire.")]
    private ?int $vehb_c_permis = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    // #[Assert\NotBlank(message:"Veuillez entrer une date valide.")]
    // Assert\NotEqualTo(['value' => '2024-02-17', 'message' => 'La date ne peut pas être égale à 2024-02-17.']),
    private ?\DateTimeInterface $vehb_c_datepermis = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champ est obligatoire.")]
    private ?string $vehb_a_nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champ est obligatoire.")]
    private ?string $vehb_a_prenom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champ est obligatoire.")]
    private ?string $vehb_a_adresse = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"Ce champ est obligatoire.")]
    private ?int $vehb_a_tel = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champ est obligatoire.")]
    private ?string $vehb_marque = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champ est obligatoire.")]
    private ?string $vehb_type = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champ est obligatoire.")]
    private ?string $vehb_matricule = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champ est obligatoire.")]
    private ?string $vehb_venant = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champ est obligatoire.")]
    private ?string $vehb_allant = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champ est obligatoire.")]
    private ?string $veha_circomstances = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Ce champ est obligatoire.")]
    private ?string $vehb_circomstances = null;

    #[ORM\Column(length: 255)]
    private ?string $photo_accident = null;

    #[ORM\Column(length: 255)]
    private ?string $photo_veha = null;

    #[ORM\Column(length: 255)]
    private ?string $photo_vehb = null;

    #[ORM\Column]
    private ?bool $blesses = null;

    #[ORM\ManyToOne(inversedBy: 'constats')]
    private ?Utilisateur $id_user = null;




     


    #[Assert\File]
    private ?UploadedFile $photoVehaFile = null;

    
    #[Assert\File]
    private ?UploadedFile $photoVehbFile = null;

 
 #[Assert\File]
                                                          private ?UploadedFile $photoAccident = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateConstat = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $signatureId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $documentId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $signerId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pdfSansSignature = null;




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVehaPermis(): ?int
    {
        return $this->veha_permis;
    }

    public function setVehaPermis(int $veha_permis): static
    {
        $this->veha_permis = $veha_permis;

        return $this;
    }

    public function getVehaVenant(): ?string
    {
        return $this->veha_venant;
    }

    public function setVehaVenant(string $veha_venant): static
    {
        $this->veha_venant = $veha_venant;

        return $this;
    }

    public function getVehaAllant(): ?string
    {
        return $this->veha_allant;
    }

    public function setVehaAllant(string $veha_allant): static
    {
        $this->veha_allant = $veha_allant;

        return $this;
    }

    public function getVehaDatepermis(): ?\DateTimeInterface
    {
        return $this->veha_datepermis;
    }

    public function setVehaDatepermis(\DateTimeInterface $veha_datepermis): static
    {
        $this->veha_datepermis = $veha_datepermis;

        return $this;
    }

    public function getVehbAssurance(): ?string
    {
        return $this->vehb_assurance;
    }

    public function setVehbAssurance(string $vehb_assurance): static
    {
        $this->vehb_assurance = $vehb_assurance;

        return $this;
    }

    public function getVehbPolice(): ?string
    {
        return $this->vehb_police;
    }

    public function setVehbPolice(string $vehb_police): static
    {
        $this->vehb_police = $vehb_police;

        return $this;
    }

    public function getVehbAgence(): ?string
    {
        return $this->vehb_agence;
    }

    public function setVehbAgence(string $vehb_agence): static
    {
        $this->vehb_agence = $vehb_agence;

        return $this;
    }

    public function getVehbAttestationVDD(): ?\DateTimeInterface
    {
        return $this->vehb_attestationVDD;
    }

    public function setVehbAttestationVDD(\DateTimeInterface $vehb_attestationVDD): static
    {
        $this->vehb_attestationVDD = $vehb_attestationVDD;

        return $this;
    }

    public function getVehbAttestationVDF(): ?\DateTimeInterface
    {
        return $this->vehb_attestationVDF;
    }

    public function setVehbAttestationVDF(\DateTimeInterface $vehb_attestationVDF): static
    {
        $this->vehb_attestationVDF = $vehb_attestationVDF;

        return $this;
    }

    public function getVehbCNom(): ?string
    {
        return $this->vehb_c_nom;
    }

    public function setVehbCNom(string $vehb_c_nom): static
    {
        $this->vehb_c_nom = $vehb_c_nom;

        return $this;
    }

    public function getVehbCPrenom(): ?string
    {
        return $this->vehb_c_prenom;
    }

    public function setVehbCPrenom(string $vehb_c_prenom): static
    {
        $this->vehb_c_prenom = $vehb_c_prenom;

        return $this;
    }

    public function getVehbCAdresse(): ?string
    {
        return $this->vehb_c_adresse;
    }

    public function setVehbCAdresse(string $vehb_c_adresse): static
    {
        $this->vehb_c_adresse = $vehb_c_adresse;

        return $this;
    }

    public function getVehbCPermis(): ?int
    {
        return $this->vehb_c_permis;
    }

    public function setVehbCPermis(int $vehb_c_permis): static
    {
        $this->vehb_c_permis = $vehb_c_permis;

        return $this;
    }

    public function getVehbCDatepermis(): ?\DateTimeInterface
    {
        return $this->vehb_c_datepermis;
    }

    public function setVehbCDatepermis(\DateTimeInterface $vehb_c_datepermis): static
    {
        $this->vehb_c_datepermis = $vehb_c_datepermis;

        return $this;
    }

    public function getVehbANom(): ?string
    {
        return $this->vehb_a_nom;
    }

    public function setVehbANom(string $vehb_a_nom): static
    {
        $this->vehb_a_nom = $vehb_a_nom;

        return $this;
    }

    public function getVehbAPrenom(): ?string
    {
        return $this->vehb_a_prenom;
    }

    public function setVehbAPrenom(string $vehb_a_prenom): static
    {
        $this->vehb_a_prenom = $vehb_a_prenom;

        return $this;
    }

    public function getVehbAAdresse(): ?string
    {
        return $this->vehb_a_adresse;
    }

    public function setVehbAAdresse(string $vehb_a_adresse): static
    {
        $this->vehb_a_adresse = $vehb_a_adresse;

        return $this;
    }

    public function getVehbATel(): ?int
    {
        return $this->vehb_a_tel;
    }

    public function setVehbATel(int $vehb_a_tel): static
    {
        $this->vehb_a_tel = $vehb_a_tel;

        return $this;
    }

    public function getVehbMarque(): ?string
    {
        return $this->vehb_marque;
    }

    public function setVehbMarque(string $vehb_marque): static
    {
        $this->vehb_marque = $vehb_marque;

        return $this;
    }

    public function getVehbType(): ?string
    {
        return $this->vehb_type;
    }

    public function setVehbType(string $vehb_type): static
    {
        $this->vehb_type = $vehb_type;

        return $this;
    }

    public function getVehbMatricule(): ?string
    {
        return $this->vehb_matricule;
    }

    public function setVehbMatricule(string $vehb_matricule): static
    {
        $this->vehb_matricule = $vehb_matricule;

        return $this;
    }

    public function getVehbVenant(): ?string
    {
        return $this->vehb_venant;
    }

    public function setVehbVenant(string $vehb_venant): static
    {
        $this->vehb_venant = $vehb_venant;

        return $this;
    }

    public function getVehbAllant(): ?string
    {
        return $this->vehb_allant;
    }

    public function setVehbAllant(string $vehb_allant): static
    {
        $this->vehb_allant = $vehb_allant;

        return $this;
    }

    public function getVehaCircomstances(): ?string
    {
        return $this->veha_circomstances;
    }

    public function setVehaCircomstances(string $veha_circomstances): static
    {
        $this->veha_circomstances = $veha_circomstances;

        return $this;
    }

    public function getVehbCircomstances(): ?string
    {
        return $this->vehb_circomstances;
    }

    public function setVehbCircomstances(string $vehb_circomstances): static
    {
        $this->vehb_circomstances = $vehb_circomstances;

        return $this;
    }

    public function getPhotoAccident()
    {
        return $this->photo_accident;
    }

    public function setPhotoAccident($photo_accident): static
    {
        $this->photo_accident = $photo_accident;

        return $this;
    }

    public function getPhotoVeha()
    {
        return $this->photo_veha;
    }

    public function setPhotoVeha($photo_veha): static
    {
        $this->photo_veha = $photo_veha;

        return $this;
    }

    public function getPhotoVehb()
    {
        return $this->photo_vehb;
    }

    public function setPhotoVehb($photo_vehb): static
    {
        $this->photo_vehb = $photo_vehb;

        return $this;
    }

    public function isBlesses(): ?bool
    {
        return $this->blesses;
    }

    public function setBlesses(bool $blesses): static
    {
        $this->blesses = $blesses;

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



    public function getPhotoVehaFile(): ?UploadedFile
    {
        return $this->photoVehaFile;
    }

    public function setPhotoVehaFile(?UploadedFile $photoVehaFile): void
    {
        $this->photoVehaFile = $photoVehaFile;
    }

    public function getPhotoVehbFile(): ?UploadedFile
    {
        return $this->photoVehbFile;
    }

    public function setPhotoVehbFile(?UploadedFile $photoVehbFile): void
    {
        $this->photoVehbFile = $photoVehbFile;
    }

    public function getPhotoAccidentFile(): ?UploadedFile
    {
        return $this->photoAccident;
    }

    public function setPhotoAccidentFile(?UploadedFile $photoAccident): void
    {
        $this->photoAccident = $photoAccident;
    }

    public function getDateConstat(): ?\DateTimeInterface
    {
        return $this->dateConstat;
    }

    public function setDateConstat(\DateTimeInterface $dateConstat): static
    {
        $this->dateConstat = $dateConstat;

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

    public function getSignatureId(): ?string
    {
        return $this->signatureId;
    }

    public function setSignatureId(?string $signatureId): static
    {
        $this->signatureId = $signatureId;

        return $this;
    }

    public function getDocumentId(): ?string
    {
        return $this->documentId;
    }

    public function setDocumentId(?string $documentId): static
    {
        $this->documentId = $documentId;

        return $this;
    }

    public function getSignerId(): ?string
    {
        return $this->signerId;
    }

    public function setSignerId(?string $signerId): static
    {
        $this->signerId = $signerId;

        return $this;
    }

    public function getPdfSansSignature(): ?string
    {
        return $this->pdfSansSignature;
    }

    public function setPdfSansSignature(?string $pdfSansSignature): static
    {
        $this->pdfSansSignature = $pdfSansSignature;

        return $this;
    }



}