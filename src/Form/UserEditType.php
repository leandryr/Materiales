<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('id', TextType::class)
            ->add('correo', TextType::class)
            ->add('usuario', TextType::class)
            ->add('contracena', TextType::class)
            ->add('type', TextType::class)
            ->add('activo', TextType::class)


        ;
    }
}
