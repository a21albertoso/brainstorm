<?php

namespace App\Form;

use App\Entity\Subida;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubidaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class, [
                'label' => 'Archivo',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-title' => 'Seleccionar foto',
                ],
            ])
            ->add('guardar', SubmitType::class, [
                'label' => 'Guardar',
                'attr' => ['class' => 'btn btn-primary mt-2'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Subida::class,
        ]);
    }
}
