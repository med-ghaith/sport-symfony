<?php

namespace App\Form;

use App\Entity\Course;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description',TextareaType ::class)
            ->add('video',TextType ::class)
            /*->add('video', FileType::class, [
                'label' => 'Image :',
                'data_class' => null,
                //mapped false so symfony does not get and set its value from the related entity
                'mapped' => false,
                //required false, so you don't have to re-upload file every time I edit the Cours
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],])*/
            ->add('save',SubmitType::class)
        ; 
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
        ]);
    }
}
