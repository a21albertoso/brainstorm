<?php

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InformacionAdicionalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Otros campos del formulario
            ->add('archivo', FileType::class, [
                'label' => 'Archivo adjunto: ',
                'required' => false,
                'mapped' => false, // No se mapea a una propiedad de la entidad
            ])
            ->add('guardar', SubmitType::class, [
                'label' => 'Guardar',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configuración de la entidad de datos del formulario
            // ...
        ]);
    }
}
