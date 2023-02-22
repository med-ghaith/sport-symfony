<?php

namespace App\Form;

use App\Entity\PrivateMessage;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrivateMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content')
//            ->add('createdAt')
            ->add('idFirstUser',EntityType::class,[
                'class'=>User::class,
                'choice_label'=>'firstName'
            ])
            ->add('idSecondUser',EntityType::class,[
                'class'=>User::class,
                'choice_label'=>'firstName'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PrivateMessage::class,
        ]);
    }
}
