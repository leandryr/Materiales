<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class BusquedaReporteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fechaEvento1', TextType::class)
            ->add('fechaEvento2', TextType::class)
            ->add('estatus', TextType::class)
            ->add('tipoReporte', TextType::class)



        ;
    }
}
