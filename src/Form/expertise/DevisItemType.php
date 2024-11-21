<?php

namespace App\Form;

use App\Entity\DevisItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class DevisItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('designation',TextType::class,[
                'constraints' => [
                    new Assert\NotBlank(message:'La désignation est obligatoire'),
                ],
            ])
            ->add('quantite',NumberType::class,[
                'constraints' => [
                    new Assert\NotBlank(message:'La quantité est obligatoire'),
                    new Assert\Positive(message:'La quantité doit être positive'),
                ],
            ])
            ->add('prix_u',NumberType::class,[
                'constraints' => [
                    new Assert\NotBlank(message:'Le prix unitaire est obligatoire'),
                    new Assert\Positive(message:'La prix unitaire doit être positive'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DevisItem::class,
        ]);
    }
}
