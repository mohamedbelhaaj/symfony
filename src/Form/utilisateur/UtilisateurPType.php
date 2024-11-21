<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Enum\typeUser;
use App\Form\Type\CustomDateType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UtilisateurPType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Surname is required',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ ]+$/',
                        'message' => 'Surname can have only letters and spaces',
                    ]),
                ],
            ])
            ->add('prenom',TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Name can not be empty',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ ]+$/',
                        'message' => 'Name can have only letters and spaces',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => "Email is required",
                    ]),
                    new Email([
                        'message' => "This Email '{{ value }}' is not valid",
                    ]),
                ],
            ])
            ->add('adresse', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => "Address can not be empty",
                    ]),
                ],
            ])
            ->add('tel', IntegerType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => "Phone number can not be empty",
                    ]),
                    new Regex([
                        'pattern' => '/^\d{8}$/',
                        'message' => "Phone number must be composed of 8 digits",
                    ]),
                ],
            ])
            ->add('date_n',CustomDateType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => "Birdthday can not be empty",
                    ]),
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'mapped' => false, // This tells Symfony not to try to map this field to any entity property
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG)',
                    ]),
                ],
            ])
        ;
        $builder->setAttributes([
            'class' => 'forms-sample'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}

