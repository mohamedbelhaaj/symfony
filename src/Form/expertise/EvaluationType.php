<?php

namespace App\Form;

use App\Entity\Evaluation;
use PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report\Text;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class EvaluationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('valeurneuf',NumberType::class,[
                'constraints' => [
                    new Assert\NotBlank(message:'La valeur neuf est obligatoire'),
                    new Assert\Positive(message:'La valeur neuf doit être positive'),
                ],
            ])
            ->add('valeurvenal',NumberType::class,[
                'constraints' => [
                    new Assert\NotBlank(message:'La valeur venal est obligatoire'),
                    new Assert\Positive(message:'La valeur venal doit être positive'),
                ],
            ])
            ->add('observation',TextType::class,[
                'constraints' => [
                    new Assert\NotBlank(message:"L'observation est obligatoire"),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evaluation::class,
        ]);
    }
}
