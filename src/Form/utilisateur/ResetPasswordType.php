<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cup', PasswordType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => "Current password is required.",
                    ]),
                ],
                'attr' => [
                    'class' => 'form-control input-box form-ensurance-header-control', // Apply form-control class for Bootstrap styling
                    'autocomplete' => 'off', // Adjust autocomplete behavior if needed
                    'id' => 'cup',
                ],
                'label' => 'Current password',
            ])
            ->add('np', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => [
                        'class' => 'form-control input-box form-ensurance-header-control', // Apply form-control class for Bootstrap styling
                        'autocomplete' => 'off', // Adjust autocomplete behavior if needed
                        'id' => 'np',
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => "New password is required.",
                        ]),
                        new Regex([
                            'pattern' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                            'message' => "The password must be composed of 8 characters, containing at least one uppercase letter, one lowercase letter, one digit, and one special character.",
                        ]),
                    ],
                    'label' => 'New password',
                ],
                'second_options' => [
                    'attr' => [
                        'class' => 'form-control input-box form-ensurance-header-control', // Apply form-control class for Bootstrap styling
                        'autocomplete' => 'off', // Adjust autocomplete behavior if needed
                        'id' => 'con_np',
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => "Confirm password is required.",
                        ]),
                        new Regex([
                            'pattern' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                            'message' => "The password must be composed of 8 characters, containing at least one uppercase letter, one lowercase letter, one digit, and one special character.",
                        ]),
                    ],
                    'label' => 'Confirm password',
                ],
                'invalid_message' => 'The passwords do not match.', // Error message if passwords don't match
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
