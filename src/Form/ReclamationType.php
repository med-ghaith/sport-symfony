<?php

namespace App\Form;

use App\Entity\Reclamation;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Gregwar\CaptchaBundle\Type\CaptchaType;


class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('object', TextType::class)
            ->add('description', TextareaType::class)        
            ->add('typeReclamation', ChoiceType::class, [
                'choices'  => [
                    'FRAUD' => 'FRAUD',
                    'RACISM' => 'RACISM',
                    'FAKEUSER' => 'FAKEUSER',
                    'VIOLENCE' => 'VIOLENCE',
                    'HARASSEMENT' => 'HARASSEMENT',
                    'SCAM' => 'SCAM'
                ],
            ])  
           ->add('captcha', CaptchaType::class);
            
             
            
            
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
