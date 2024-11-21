<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomDateType extends AbstractType
{
    public function getParent()
    {
        return DateType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('widget', 'single_text');
    }
}
