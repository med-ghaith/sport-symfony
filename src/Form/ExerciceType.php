<?php

namespace App\Form;

use App\Entity\Equipment;
use App\Entity\Exercice;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExerciceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('imageUrl', FileType::class,[
                'mapped'=>false ,
                'label'=> 'Upload your exercice image'
            ])
            ->add('description')
            ->add('difficultyLevel')
            ->add('numberOfSets')
            ->add('numberOfRepetition')
            ->add('restTime')
//            ->add('muscles')
            ->add('Equipments',EntityType::class,[
                'class'=>Equipment::class,
                'choice_label'=>'name',
                'multiple'=>true,
                'expanded'=>false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Exercice::class,
        ]);
    }
}
