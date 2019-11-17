<?php

namespace App\Form;

use App\Entity\Saloon;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class SubscribeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('promo', TextType::class, array(
                'label' => 'Code promo ?',
                'required' => false,
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'S\'abonner',
                'attr' => array(
                    'class' => 'btn btn-primary btn-block',
                )
            ))
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Saloon::class,
        ));
    }
}
