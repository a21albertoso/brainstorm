<?php

namespace App\Form;

use App\Entity\Tema;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TemaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nombre')
        ->add('contenido', TextareaType::class)
        ->add('submit', SubmitType::class, [
            'label' => 'Crear tema',
            'attr' => ['class' => 'btn btn-primary'],
            'row_attr' => [
                'class' => 'mt-3', 
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tema::class,
        ]);
    }
}
