<?php

namespace App\Form;

use App\Entity\Constat;
use Doctrine\DBAL\Types\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ConstatUsrType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('veha_permis')
            ->add('veha_venant')
            ->add('veha_allant')
            ->add('veha_datepermis')
            ->add('vehb_assurance')
            ->add('vehb_police')
            ->add('vehb_agence')
            ->add('vehb_attestationVDD')
            ->add('vehb_attestationVDF')
            ->add('vehb_c_nom')
            ->add('vehb_c_prenom')
            ->add('vehb_c_adresse')
            ->add('vehb_c_permis')
            ->add('vehb_c_datepermis')
            ->add('vehb_a_nom')
            ->add('vehb_a_prenom')
            ->add('vehb_a_adresse')
            ->add('vehb_a_tel')
            ->add('vehb_marque')
            ->add('vehb_type')
            ->add('vehb_matricule')
            ->add('vehb_venant')
            ->add('vehb_allant')
            ->add('veha_circomstances')
            ->add('vehb_circomstances')
            //->add('photo_accident')
            //     'attr' => [
            //         'style' => 'display: none;',
            //     ],
            // ])
            // ->add('photo_veha',null,[
            //     'attr' => [
            //         'style' => 'display: none;',
            //     ],
            // ])

            ->add('photo_veha_file', FileType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez télécharger la photo du véhicule A.',
                    ]),
                ],
            ])
            ->add('photo_vehb_file', FileType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez télécharger la photo du véhicule B.',
                    ]),
                ],
            ])
            ->add('photo_accident_file', FileType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => "Veuillez télécharger la photo d'accident.",
                    ]),
                ],
            ])
          //  ->add('photo_vehb')
            ->add('blesses')
            //->add('id_user')
            ->add('save', SubmitType::class, ['label' => 'Enregistrer Constat']);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Constat::class,
        ]);
    }
}