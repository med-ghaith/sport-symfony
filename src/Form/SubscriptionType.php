<?php

namespace App\Form;

use App\Entity\Subscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->add('startDate')
            ->add('validity',ChoiceType::class, [
                'choices'  => [
                    'Choisisez nombre de mois de votre abonnement' => null,                   
                     '1 mois'=>"1 mois",
                     '2 mois'=>"2 mois",
                     '3 mois'=>"3 mois",
                     '4 mois'=>"4 mois",
                     '5 mois'=>"5 mois",
                     '6 mois'=>"6 mois",
                     '7 mois'=>"7 mois",
                     '8 mois'=>"8 mois",
                     '9 mois'=>"9 mois",
                     '10 mois'=>"10 mois",
                     '11 mois'=>"11 mois",
                     '12 mois'=>"12 mois",
                ],
            ])
            ->add('save',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subscription::class,
        ]);
    }
}
