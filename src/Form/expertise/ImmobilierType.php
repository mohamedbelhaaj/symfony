<?php

namespace App\Form;

use App\Entity\Immobilier;
use App\Enum\typeImm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints as Assert;

class ImmobilierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_fiscal',NumberType::class,[
                'constraints' => [
                    new Assert\NotBlank(message:"L'identifiant fiscale est obligatoire"),
                ],
            ])
            ->add('adresse',TextType::class,[
                'constraints' => [
                    new Assert\NotBlank(message:"L'adresse' est obligatoire"),
                ],
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Villa' => typeImm::V,
                    'Etage Villa' => typeImm::EV,
                    'Appartement' => typeImm::A,
                ],
                'constraints' => [
                    new Assert\NotBlank(message:'Le type est obligatoire'),
                ],
                'placeholder' => "Sélectionnez le type de l'immobilier",
            ])
            ->add('superficie',NumberType::class,[
                'constraints' => [
                    new Assert\NotBlank(message:'La superficie est obligatoire'),
                ],
            ])
            ->add('titre_prop',FileType::class, [
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
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Immobilier::class,
        ]);
    }
}
