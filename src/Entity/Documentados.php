<?php

namespace App\Entity;

use App\Repository\DocumentadosRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DocumentadosRepository::class)
 */
class Documentados
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $claim;

    /**
     * @ORM\ManyToOne(targetEntity=Localidad::class)
     */
    private $localidad;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $codigo;

    /**
     * @ORM\ManyToOne(targetEntity=Planta::class)
     */
    private $planta;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $numero;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cantidad;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaNotificacion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $perdidaSinFlete;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $perdidaConFlete;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $area;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $estatus;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $documentacionFaltante;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClaim(): ?string
    {
        return $this->claim;
    }

    public function setClaim(?string $claim): self
    {
        $this->claim = $claim;

        return $this;
    }

    public function getLocalidad(): ?Localidad
    {
        return $this->localidad;
    }

    public function setLocalidad(?Localidad $localidad): self
    {
        $this->localidad = $localidad;

        return $this;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(?string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getPlanta(): ?Planta
    {
        return $this->planta;
    }

    public function setPlanta(?Planta $planta): self
    {
        $this->planta = $planta;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(?string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getCantidad(): ?string
    {
        return $this->cantidad;
    }

    public function setCantidad(?string $cantidad): self
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function getFechaNotificacion(): ?\DateTimeInterface
    {
        return $this->fechaNotificacion;
    }

    public function setFechaNotificacion(?\DateTimeInterface $fechaNotificacion): self
    {
        $this->fechaNotificacion = $fechaNotificacion;

        return $this;
    }

    public function getPerdidaSinFlete(): ?string
    {
        return $this->perdidaSinFlete;
    }

    public function setPerdidaSinFlete(?string $perdidaSinFlete): self
    {
        $this->perdidaSinFlete = $perdidaSinFlete;

        return $this;
    }

    public function getPerdidaConFlete(): ?string
    {
        return $this->perdidaConFlete;
    }

    public function setPerdidaConFlete(?string $perdidaConFlete): self
    {
        $this->perdidaConFlete = $perdidaConFlete;

        return $this;
    }


    public function getArea(): ?string
    {
        return $this->area;
    }

    public function setArea(?string $area): self
    {
        $this->area = $area;

        return $this;
    }

    public function getEstatus(): ?string
    {
        return $this->estatus;
    }

    public function setEstatus(?string $estatus): self
    {
        $this->estatus = $estatus;

        return $this;
    }

    public function getDocumentacionFaltante(): ?string
    {
        return $this->documentacionFaltante;
    }

    public function setDocumentacionFaltante(?string $documentacionFaltante): self
    {
        $this->documentacionFaltante = $documentacionFaltante;

        return $this;
    }
}
