<?php

namespace App\Form;

use App\Entity\Slot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use App\Form\CustomerType;
use App\Entity\Price;
use App\Entity\User;

class SlotType extends AbstractType
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
            ->add('price', EntityType::class, array(
                'label' => 'Tarif',
                'class' => Price::class,
                'choices' => $options['data']->getSaloon()->getPrices(),
            ))
            ->add('worker', EntityType::class, array(
                'label' => 'Coiffeur',
                'class' => User::class,
                'choices' => $options['data']->getSaloon()->getWorkers(),
            ))
            ->add('customer', CustomerType::class, array(
                'label' => 'Informations client'
            ))
            ->add('isPaid', CheckboxType::class, array(
                'label' => 'Paiement effectué',
                'required' => false
            ))
            ->add('submit', SubmitType::class, array(
                'label' => $options['labelButton'],
                'attr' => array(
                    'class' => 'btn btn-primary btn-block',
                )
            ))

            ->addEventListener(FormEvents::SUBMIT, array($this, 'onSubmit'))
        ;
    }

    public function onSubmit(FormEvent $event)
    {
        $slot = $event->getData();

        if (!is_object($slot->getStart())) {
            $slot->setStart(new \Datetime($slot->getStart()));
        }

        $end = clone $slot->getStart();
        $slot->setEnd($end->add(new \DateInterval('PT' . $slot->getPrice()->getDuration() . 'M')));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Slot::class,
            'labelButton' => 'Modifier',
        ]);
    }
}
