<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('foto', FileType::class, [
                'label' => 'Foto de perfil',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-title' => 'Seleccionar foto',
                ],
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Por favor, sube un archivo PNG o JPEG válido.',
                    ]),
                ],
            ])
            ->add('guardar', SubmitType::class, [
                'label' => 'Guardar',
                'attr' => ['class' => 'btn btn-primary mt-2'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
