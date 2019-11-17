<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\Saloon;
use App\Form\Field\ScheduleType;
use App\Form\Field\PriceType;

class SaloonAdminType extends AbstractType
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
            ->add('schedules', CollectionType::class, array(
                'entry_type' => ScheduleType::class
            ))
            ->add('prices', CollectionType::class, array(
                'entry_type' => PriceType::class,
                'allow_add' => true,
                'by_reference' => false
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

    public function getBlockPrefix()
    {
        return 'saloon';
    }
}
