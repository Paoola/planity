<?php

namespace App\Form\Field;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use App\Entity\Schedule;

class ScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start', TimeType::class, array(
                'with_seconds' => false,
                'widget'       => 'single_text',
                'label'        => 'Début à'
            ))
            ->add('end', TimeType::class, array(
                'with_seconds' => false,
                'widget'       => 'single_text',
                'label'        => 'Fin à'
            ))
            ->add('dayOff', CheckboxType::class, array(
                'label' => 'Journée off',
                'required' => false
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Schedule::class,
        ]);
    }
}
