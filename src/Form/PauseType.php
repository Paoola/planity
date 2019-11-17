<?php

namespace App\Form;

use App\Entity\Pause;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PauseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start', DateTimeType::class, array(
                'label' => 'Commence à',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy HH:mm',
                'attr' => array(
                    'class' => 'datetime-picker'
                ),
            ))
            ->add('end', DateTimeType::class, array(
                'label' => 'Termine à',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy HH:mm',
                'attr' => array(
                    'class' => 'datetime-picker'
                ),
            ))
            ->add('everyweek', CheckboxType::class, array(
                'label' => 'Chaque semaine'
            ))
            ->add('worker', EntityType::class, array(
                'label' => 'Coiffeur',
                'class' => User::class,
                'choices' => $options['data']->getSaloon()->getWorkers(),
            ))
            ->add('submit', SubmitType::class, array(
                'label' => $options['labelButton'],
                'attr' => array(
                    'class' => 'btn btn-primary btn-block',
                )
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pause::class,
            'labelButton' => 'Modifier',
        ]);
    }
}
