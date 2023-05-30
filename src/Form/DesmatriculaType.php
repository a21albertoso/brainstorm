<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Asignatura;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DesmatriculaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $users = $options['users'];
        $asignaturas = $options['asignaturas'];

        $builder
            ->add('user', ChoiceType::class, [
                'choices' => $users,
                'choice_label' => 'email', // Ajusta esto según la propiedad de usuario que desees mostrar en las opciones
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('asignaturas', ChoiceType::class, [
                'choices' => $asignaturas,
                'choice_label' => 'nombre',
                'multiple' => true,
                'expanded' => true,
                'choice_attr' => function($choice, $key, $value){
                    return [
                        'class' => 'custom-control-input'
                    ];
                },
                'row_attr' => ['class' => 'form-group'], 
            ])
            ->add('confirmar', SubmitType::class, [
                'label' => 'Confirmar Desmatrícula',
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'users' => [],
            'asignaturas' => [],
        ]);
    }
}
