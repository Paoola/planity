<?php

namespace App\Form;

use App\Entity\Saloon;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class SaloonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Nom de votre salon',
                'attr' => array(
                    'placeholder' => 'Cut cut'
                )
            ))
            ->add('location', TextType::class, array(
                'label' => 'Adresse',
            ))
            ->add('phoneNumber', TextType::class, array(
                'label' => 'Numéro de téléphone',
                'required' => false,
            ))
            ->add('facebookLink', TextType::class, array(
                'label' => 'Lien page facebook',
                'required' => false,
            ))
            ->add('onlinePayment', CheckboxType::class, array(
                'label' => 'Paiement en ligne',
                'required' => false
            ))
            ->add('imageFile', VichImageType::class, array(
                'label' => 'Photo principale',
                'required' => false
            ))
            ->add('customText', TextAreaType::class, array(
                'label' => 'Informations supplémentaires',
                'required' => false
            ))

            ->add('locationId', HiddenType::class)
            ->add('latitude', HiddenType::class)
            ->add('longitude', HiddenType::class)

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
            'data_class' => Saloon::class,
            'labelButton' => 'Ajouter',
        ]);
    }
}
