<?php

namespace App\Form;

use App\Entity\UserAsignatura;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class UserAsignaturaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('user', ChoiceType::class, [
            'choices' => $options['users'],
            'choice_label' => function ($user) {
                // Acceder al objeto User y obtener el email
                return $user->getEmail();
            },
            'label' => 'User',
        ])
        ->add('asignatura', ChoiceType::class, [
            'choices' => $options['asignaturas'],
            'choice_label' => function ($asignatura) {
                // Acceder al objeto Asignatura y obtener el nombre
                return $asignatura->getNombre();
            },
            'label' => 'Asignatura',
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Guardar',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserAsignatura::class,
            'users' => [], // Array de opciones para los usuarios
            'asignaturas' => [], // Array de opciones para las asignaturas
        ]);
    }
}
