<?php

namespace App\Form;

use App\Entity\Price;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $workers = $options['data']->getSaloon()->getWorkers()->getValues();

        $builder
            ->add('name', TextType::class, array(
                'label' => 'Titre'
            ))
            ->add('duration', TextType::class, array(
                'label' => 'Durée (en min)'
            ))
            ->add('amount', TextType::class, array(
                'label' => 'Montant (en €)'
            ))
            ->add('workers', EntityType::class, array(
                'label' => 'Coiffeurs',
                'class' => User::class,
                'choices' => $workers,
                'choice_label' => 'username',
                'multiple' => true
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
            'data_class' => Price::class,
            'labelButton' => 'Modifier',
        ]);
    }
}
