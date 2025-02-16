<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DocumentadoEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('id', TextType::class)

            ->add('localidad', TextType::class)
            ->add('claim', TextType::class)
            ->add('codigo', TextType::class)
            ->add('planta', TextType::class)
            ->add('numero', TextType::class)
            ->add('cantidad', TextType::class)
            ->add('fechaNotificacion', TextType::class)
            ->add('perdidaSinFlete', TextType::class)
            ->add('perdidaConFlete', TextType::class)
            ->add('area', TextType::class)
            ->add('estatus', TextType::class)
            ->add('documentacionFaltante', TextType::class)
                    ;
    }
}
