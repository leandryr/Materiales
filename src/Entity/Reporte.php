<?php

namespace App\Entity;

use App\Repository\ReporteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReporteRepository::class)
 */
class Reporte
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tipo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reclamadoUSD;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reclamadoMXN;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $excedenteMXN;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $estimadoMXN;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $aceptadoMXN;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rechazadoMXN;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $canceladoMXN;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $flete;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaEvento;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaEmision;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fecha1;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fecha2;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fecha3;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaEscalacion;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaResolucion;


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
    private $observaciones;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $actualizacion;

    /**
     * @ORM\ManyToOne(targetEntity=Localidad::class)
     */
    private $localidad;

    /**
     * @ORM\ManyToOne(targetEntity=Transportista::class)
     */
    private $transportista;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaRespuesta;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaSolicitud;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaAplicacion;

    /**
     * @ORM\Column(type="boolean")
     */
    private $documentado;

    /**
     * @ORM\Column(type="boolean")
     */
    private $mayor45;

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


    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(?string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getReclamadoUSD(): ?string
    {
        return $this->reclamadoUSD;
    }

    public function setReclamadoUSD(?string $reclamadoUSD): self
    {
        $this->reclamadoUSD = $reclamadoUSD;

        return $this;
    }

    public function getReclamadoMXN(): ?string
    {
        return $this->reclamadoMXN;
    }

    public function setReclamadoMXN(?string $reclamadoMXN): self
    {
        $this->reclamadoMXN = $reclamadoMXN;

        return $this;
    }

    public function getExcedenteMXN(): ?string
    {
        return $this->excedenteMXN;
    }

    public function setExcedenteMXN(?string $excedenteMXN): self
    {
        $this->excedenteMXN = $excedenteMXN;

        return $this;
    }

    public function getEstimadoMXN(): ?string
    {
        return $this->estimadoMXN;
    }

    public function setEstimadoMXN(?string $estimadoMXN): self
    {
        $this->estimadoMXN = $estimadoMXN;

        return $this;
    }

    public function getAceptadoMXN(): ?string
    {
        return $this->aceptadoMXN;
    }

    public function setAceptadoMXN(?string $aceptadoMXN): self
    {
        $this->aceptadoMXN = $aceptadoMXN;

        return $this;
    }

    public function getRechazadoMXN(): ?string
    {
        return $this->rechazadoMXN;
    }

    public function setRechazadoMXN(?string $rechazadoMXN): self
    {
        $this->rechazadoMXN = $rechazadoMXN;

        return $this;
    }

    public function getCanceladoMXN(): ?string
    {
        return $this->canceladoMXN;
    }

    public function setCanceladoMXN(?string $canceladoMXN): self
    {
        $this->canceladoMXN = $canceladoMXN;

        return $this;
    }

    public function getFlete(): ?string
    {
        return $this->flete;
    }

    public function setFlete(?string $flete): self
    {
        $this->flete = $flete;

        return $this;
    }

    public function getFechaEvento(): ?\DateTimeInterface
    {
        return $this->fechaEvento;
    }

    public function setFechaEvento(?\DateTimeInterface $fechaEvento): self
    {
        $this->fechaEvento = $fechaEvento;

        return $this;
    }

    public function getFechaEmision(): ?\DateTimeInterface
    {
        return $this->fechaEmision;
    }

    public function setFechaEmision(?\DateTimeInterface $fechaEmision): self
    {
        $this->fechaEmision = $fechaEmision;

        return $this;
    }

    public function getFecha1(): ?\DateTimeInterface
    {
        return $this->fecha1;
    }

    public function setFecha1(?\DateTimeInterface $fecha1): self
    {
        $this->fecha1 = $fecha1;

        return $this;
    }

    public function getFecha2(): ?\DateTimeInterface
    {
        return $this->fecha2;
    }

    public function setFecha2(?\DateTimeInterface $fecha2): self
    {
        $this->fecha2 = $fecha2;

        return $this;
    }

    public function getFecha3(): ?\DateTimeInterface
    {
        return $this->fecha3;
    }

    public function setFecha3(?\DateTimeInterface $fecha3): self
    {
        $this->fecha3 = $fecha3;

        return $this;
    }

    public function getFechaEscalacion(): ?\DateTimeInterface
    {
        return $this->fechaEscalacion;
    }

    public function setFechaEscalacion(?\DateTimeInterface $fechaEscalacion): self
    {
        $this->fechaEscalacion = $fechaEscalacion;

        return $this;
    }

    public function getFechaResolucion(): ?\DateTimeInterface
    {
        return $this->fechaResolucion;
    }

    public function setFechaResolucion(?\DateTimeInterface $fechaResolucion): self
    {
        $this->fechaResolucion = $fechaResolucion;

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

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function setObservaciones(?string $observaciones): self
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    public function getActualizacion(): ?\DateTimeInterface
    {
        return $this->actualizacion;
    }

    public function setActualizacion(\DateTimeInterface $actualizacion): self
    {
        $this->actualizacion = $actualizacion;

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

    public function getTransportista(): ?Transportista
    {
        return $this->transportista;
    }

    public function setTransportista(?Transportista $transportista): self
    {
        $this->transportista = $transportista;

        return $this;
    }

    public function getFechaRespuesta(): ?\DateTimeInterface
    {
        return $this->fechaRespuesta;
    }

    public function setFechaRespuesta(?\DateTimeInterface $fechaRespuesta): self
    {
        $this->fechaRespuesta = $fechaRespuesta;

        return $this;
    }

    public function getFechaSolicitud(): ?\DateTimeInterface
    {
        return $this->fechaSolicitud;
    }

    public function setFechaSolicitud(?\DateTimeInterface $fechaSolicitud): self
    {
        $this->fechaSolicitud = $fechaSolicitud;

        return $this;
    }

    public function getFechaAplicacion(): ?\DateTimeInterface
    {
        return $this->fechaAplicacion;
    }

    public function setFechaAplicacion(?\DateTimeInterface $fechaAplicacion): self
    {
        $this->fechaAplicacion = $fechaAplicacion;

        return $this;
    }

    public function getDocumentado(): ?bool
    {
        return $this->documentado;
    }

    public function setDocumentado(bool $documentado): self
    {
        $this->documentado = $documentado;

        return $this;
    }

    public function getMayor45(): ?bool
    {
        return $this->mayor45;
    }

    public function setMayor45(bool $mayor45): self
    {
        $this->mayor45 = $mayor45;

        return $this;
    }
}
