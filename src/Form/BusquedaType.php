<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class BusquedaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('localidad', TextType::class)
            ->add('planta', TextType::class)
            ->add('tipo', TextType::class)
            ->add('descripcion', TextType::class)
            ->add('transportista', TextType::class)
            ->add('busqueda', TextType::class)
            ->add('fechaEvento', TextType::class)
            ->add('fechaEvento2', TextType::class)

            ->add('fechaEmision', TextType::class)
            ->add('fechaEmision2', TextType::class)

            ->add('fechaRespuesta', TextType::class)
            ->add('fechaRespuesta2', TextType::class)

            ->add('fechaPago', TextType::class)
            ->add('fechaPago2', TextType::class)

            ->add('estatus', TextType::class)
            ->add('escalado', TextType::class)
            ->add('ruta', TextType::class)
            ->add('anoEvento', TextType::class)
            ->add('anoAsignacion', TextType::class)
            ->add('anoDocumentacion', TextType::class)

            ->add('pagina', TextType::class)

        ;
    }
}
