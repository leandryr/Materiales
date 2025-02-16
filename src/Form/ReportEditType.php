<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ReportEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('id', TextType::class)

            ->add('localidad', TextType::class)
            ->add('claim', TextType::class)
            ->add('transportista', TextType::class)
            ->add('tipo', TextType::class)
            ->add('reclamadoUSD', TextType::class)
            ->add('reclamadoMXN', TextType::class)
            ->add('excedenteMXN', TextType::class)
            ->add('estimadoMXN', TextType::class)
            ->add('rechazadoMXN', TextType::class)
            ->add('aceptadoMXN', TextType::class)
            ->add('canceladoMXN', TextType::class)
            ->add('flete', TextType::class)
            ->add('fechaEvento', TextType::class)
            ->add('fechaEmision', TextType::class)
            ->add('anoEvento', TextType::class)
            ->add('anoAsignacion', TextType::class)
            ->add('anoDocumentacion', TextType::class)
            ->add('formaPago', TextType::class)

            ->add('fechaRespuesta', TextType::class)
            ->add('fechaSolicitud', TextType::class)
            ->add('fechaAplicacion', TextType::class)
            ->add('fechaEscalacion', TextType::class)
            ->add('fechaResolucion', TextType::class)
            ->add('area', TextType::class)
            ->add('estatus', TextType::class)
            ->add('observaciones', TextType::class)
        ;
    }
}
