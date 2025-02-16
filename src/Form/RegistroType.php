<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder


            ->add('localidad', TextType::class)
            ->add('planta', TextType::class)
            ->add('tipo', TextType::class)
            ->add('descripcion', TextType::class)
            ->add('transportista', TextType::class)
            ->add('referencia', TextType::class)
            ->add('reclamadoUSD', TextType::class)
            ->add('reclamadoMXN', TextType::class)
            ->add('aceptado', TextType::class)
            ->add('recuperado', TextType::class)
            ->add('ajustes', TextType::class)
            ->add('reclamoDocumentacion', TextType::class)
            ->add('reclamoProceso', TextType::class)
            ->add('ajuste', TextType::class)
            ->add('cancelado', TextType::class)
            ->add('flete', TextType::class)
            ->add('menores', TextType::class)
            ->add('excedente', TextType::class)
            ->add('estimado', TextType::class)
            ->add('fechaEvento', TextType::class)
            ->add('fechaAsignacion', TextType::class)
            ->add('fechaDocumentacion', TextType::class)
            ->add('fechaEmision', TextType::class)
            ->add('fechaRespuesta', TextType::class)
            ->add('fechaAviso', TextType::class)
            ->add('fechaAplicacion', TextType::class)
            ->add('estatus', TextType::class)
            ->add('tipoMaterial', TextType::class)
            ->add('escalado', TextType::class)
            ->add('area', TextType::class)
            ->add('fechaEscalacion', TextType::class)
            ->add('fechaResolucion', TextType::class)
            ->add('proveedor', TextType::class)
            ->add('ruta', TextType::class)
            ->add('caja', TextType::class)
            ->add('comentarios', TextType::class)
            ->add('observaciones', TextType::class)
            ->add('anoEvento', TextType::class)
            ->add('anoAsignacion', TextType::class)
            ->add('anoDocumentacion', TextType::class)
            ->add('formaPago', TextType::class)

        ;
    }
}
