<?php

// namespace App\Form;

// use App\Entity\Assurance;
// use Symfony\Component\Form\AbstractType;
// use Symfony\Component\Form\FormBuilderInterface;
// use Symfony\Component\OptionsResolver\OptionsResolver;

// class AssuranceType extends AbstractType
// {
//     public function buildForm(FormBuilderInterface $builder, array $options): void
//     {
//         $builder
//             ->add('type')
//             ->add('date_d')
//             ->add('date_f')
//             ->add('prix')
//             ->add('id_user')
//             ->add('id_immobilier')
    //         ->add('id_vehicule')
    //     ;
    // }

    // public function configureOptions(OptionsResolver $resolver): void
    // {
    //     $resolver->setDefaults([
    //         'data_class' => Assurance::class,
    //     ]);
    // }
// }


namespace App\Form;

use App\Entity\Assurance;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

// class AssuranceType extends AbstractType
// {
//     public function buildForm(FormBuilderInterface $builder, array $options): void
//     {
//         $builder
//             ->add('type')
//             // , ChoiceType::class, [
//             //     'choices' => [
//             //         'Assurance Véhicule' => 'assv',
//             //         'Assurance Immobilière' => 'assim',
//             //         'Assurance Vie' => 'assvi',
//             //     ],
//             // ])
//             ->add('date_d')
//             ->add('date_f')
//             ->add('prix')
//             // ->add('id_user')
//             ->add('id_immobilier')
//             ->add('id_vehicule', EntityType::class, [
//                 'class' => 'App\Entity\Vehicule',
//                 'choice_label' => 'matricule', 
//                 'placeholder' => 'Sélectionnez un véhicule',
//                 'required' => false,
//                 'choices' => $options['vehicules'],
//             ])
//             ->add('save',SubmitType::class,['label' => 'Enregistrer Assurance'])
//         ;
//     }

//     public function configureOptions(OptionsResolver $resolver): void
//     {
//         $resolver->setDefaults([
//             'data_class' => Assurance::class,
//             'vehicules' => null,
//         ]);
//     }




// AssuranceType.php
// ...

class AssuranceType extends AbstractType
{
    // public function buildForm(FormBuilderInterface $builder, array $options): void
    // {
    //     $builder
    //         ->add('type')
    //         ->add('date_d')
    //         ->add('date_f')
    //         ->add('prix')
    //         ->add('id_immobilier')
    //         ->add('id_vehicule', EntityType::class, [
    //             'class' => 'App\Entity\Vehicule',
    //             'choice_label' => 'matricule',
    //             'placeholder' => 'Sélectionnez un véhicule',
    //             'required' => false,
    //             'choices' => $options['vehicules'],
    //             'choice_value' => 'matricule', // Ajoutez cette ligne
    //         ])
    //         ->add('save', SubmitType::class, ['label' => 'Enregistrer Assurance']);
    // }

    // public function configureOptions(OptionsResolver $resolver): void
    // {
    //     $resolver->setDefaults([
    //         'data_class' => Assurance::class,
    //         'vehicules' => null,
    //     ]);
    // }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('type', ChoiceType::class, [
            'placeholder' => "Sélectionner un type d'assurance",
            'choices' => [
                'Assurance Véhicule' => 'Assurance Véhicule',
                'Assurance Immobilière' => 'Assurance Immobilière',
                'Assurance Vie' => 'Assurance Vie',
            ],
        ])
            ->add('date_d')
            ->add('date_f')
            ->add('prix')



            ->add('id_immobilier', EntityType::class, [
                'class' => 'App\Entity\Immobilier',
                'choice_label' => 'id_fiscal',
                'placeholder' => 'Sélectionnez un immobilier',
                'required' => false,
                'choices' => $options['immobs'],
                'choice_value' => 'id_fiscal', 
                // 'disabled' => true,
                // 'attr' => [
                //     'class' => 'disable-on-click',
                //     'style' => 'background-color: transparent;',],
            ])
            ->add('id_vehicule', EntityType::class, [
                'class' => 'App\Entity\Vehicule',
                'choice_label' => 'matricule',
                'placeholder' => 'Sélectionnez un véhicule',
                'required' => false,
                'choices' => $options['vehicules'],
                'choice_value' => 'matricule',
                // 'disabled' => true,
                // 'attr' => [
                //     'class' => 'disable-on-click',
                //     'style' => 'background-color: transparent;',],
            ])

            
            
            
            ->add('save', SubmitType::class, ['label' => 'Enregistrer Assurance']);




            

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Assurance::class,
            'vehicules' => null,
            'immobs' =>null,
        ]);
    }
}