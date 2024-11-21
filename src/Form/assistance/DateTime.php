<?php

// src/Form/Type/DateHourType.php

// src/Form/Type/DateHourType.php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateHourType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'widget' => 'single_text',
            'html5' => true,
            'input' => 'datetime',
            'format' => 'yyyy-MM-dd HH:00', // Only display date and hour
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // Remove seconds from the input field step attribute
        $view->vars['type'] = 'datetime-local';
        $view->vars['attr']['step'] = 3600; // 1 hour
    }

    public function getParent()
    {
        return \Symfony\Component\Form\Extension\Core\Type\DateTimeType::class;
    }
}

