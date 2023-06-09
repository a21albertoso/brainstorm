<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email: ',
            ])
            ->add('password', PasswordType::class, [
                'attr' => ['autocomplete' => 'new-password'],
                'label' => 'Password: ',
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Administration of Role:',
                'required' => false,
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Admin' => 'ROLE_ADMIN',
                ],
                'attr' => [
                    'class' => 'form-control', // Agrega la clase "form-control" para darle el estilo básico de Bootstrap a los campos de entrada.
                ],
                'expanded' => false,
                'multiple' => true,
                'label_attr' => [
                    'class' => 'font-weight-bold', // Agrega la clase "font-weight-bold" para hacer el texto del label en negrita.
                ],
                'row_attr' => [
                    'class' => 'mt-3', // Agrega la clase "mt-3" para agregar un margen superior de 3 unidades (puedes ajustar el valor según tus necesidades).
                ],
            ])
            
            ->add('submit', SubmitType::class, [
                'label' => 'Register'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
