<?php

// src/Form/PublicationType.php

namespace App\Form;

use App\Entity\Publication;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PublicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a title',
                    ]),
                ],
            ])
            ->add('contenu', TextType::class, [
                'label' => 'Contenu',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter content',
                    ]),
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'mapped' => false, // This tells Symfony not to try to map this field to any entity property
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG)',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Publication::class,
        ]);
    }
}
