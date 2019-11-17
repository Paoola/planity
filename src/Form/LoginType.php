<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('_username', TextType::class, array(
                'label' => 'Email'
            ))
            ->add('_password', PasswordType::class, array(
                'label' => 'Mot de passe'
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Connexion',
                'attr' => array(
                    'class' => 'btn btn-primary btn-block'
                )
            ))
        ;
    }

    public function getBlockPrefix()
    {
        return null;
    }
}
