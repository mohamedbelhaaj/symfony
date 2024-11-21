<?php

namespace App\Form;

use App\Entity\Vehicule;
use App\Enum\typeVeh;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints as Assert;

class VehiculeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('matricule',NumberType::class,[
                'constraints' => [
                    new Assert\NotBlank(message:"La matricule est obligatoire"),
                ],
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Voiture' => typeVeh::V,
                    'Moto' => typeVeh::M,
                    'Camion' => typeVeh::C,
                    'Bateau' => typeVeh::B,
                ],
                'constraints' => [
                    new Assert\NotBlank(message:'Le type est obligatoire'),
                ],
                'placeholder' => 'Sélectionnez le type de véhicule',
            ])
            ->add('modele',  ChoiceType::class, [
                'choices' => [
                    'Ford' => 'Ford',
                    'Toyota' => 'Toyota',
                    'Honda' => 'Honda',
                    'BMW' => 'BMW',
                    'Mercedes' => "Mercedes",
                    'Range Rover' => "Range Rover",
                    'Kia' => "Kia",
                    "Ibiza" => "Ibiza",
                    "Hyundai" => "Hyundai",
                    "Haval" => "Haval",
                    "Symbole" => "Symbole",
                    "Clio" => "clio",
                    "Citroen" => "Citroen",
                ],
                'constraints' => [
                    new Assert\NotBlank(message:'Le modèle est obligatoire'),
                ],
                'placeholder' => 'Veuillez choisir une marque de véhicule valide',
                ])
            ->add('carte_grise' ,FileType::class, [
                'label' => 'Image',
                'required' => true, // Set to true if the image is mandatory
                'mapped' => false, // This tells Symfony not to try to map this field to any entity property
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG)',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class,
        ]);
    }
}
