<?php

namespace App\Entity;

use App\Repository\RegistroRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RegistroRepository::class)
 */
class Registro
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $actualizacion;

    /**
     * @ORM\ManyToOne(targetEntity=Localidad::class)
     */
    private $localidad;

    /**
     * @ORM\ManyToOne(targetEntity=Planta::class)
     */
    private $planta;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $tipo;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descripcion;

    /**
     * @ORM\ManyToOne(targetEntity=Transportista::class)
     */
    private $transportista;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $referencia;

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
    private $aceptado;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $recuperado;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ajustes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reclamoDocumentacion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reclamoProceso;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ajuste;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cancelado;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $flete;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $menores;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $excedente;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $estimado;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaEvento;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaAsignacion;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaDocumentacion;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaEmision;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaRespuesta;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaAviso;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaAplicacion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $estatus;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tipoMaterial;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $escalado;

    /**
     * @ORM\ManyToOne(targetEntity=Area::class)
     */
    private $area;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaEscalacion;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fechaResolucion;

    /**
     * @ORM\ManyToOne(targetEntity=Proveedor::class)
     */
    private $proveedor;

    /**
     * @ORM\ManyToOne(targetEntity=Ruta::class)
     */
    private $ruta;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $caja;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comentarios;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observaciones;

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
    private $fechaCheque;

     /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $anoEvento;

     /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $anoAsignacion;

     /**
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $anoDocumentacion;

     /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $formaPago;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getPlanta(): ?Planta
    {
        return $this->planta;
    }

    public function setPlanta(?Planta $planta): self
    {
        $this->planta = $planta;

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

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

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

    public function getReferencia(): ?string
    {
        return $this->referencia;
    }

    public function setReferencia(string $referencia): self
    {
        $this->referencia = $referencia;

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

    public function getAceptado(): ?string
    {
        return $this->aceptado;
    }

    public function setAceptado(?string $aceptado): self
    {
        $this->aceptado = $aceptado;

        return $this;
    }

    public function getRecuperado(): ?string
    {
        return $this->recuperado;
    }

    public function setRecuperado(?string $recuperado): self
    {
        $this->recuperado = $recuperado;

        return $this;
    }

    public function getAjustes(): ?string
    {
        return $this->ajustes;
    }

    public function setAjustes(?string $ajustes): self
    {
        $this->ajustes = $ajustes;

        return $this;
    }

    public function getReclamoDocumentacion(): ?string
    {
        return $this->reclamoDocumentacion;
    }

    public function setReclamoDocumentacion(?string $reclamoDocumentacion): self
    {
        $this->reclamoDocumentacion = $reclamoDocumentacion;

        return $this;
    }

    public function getReclamoProceso(): ?string
    {
        return $this->reclamoProceso;
    }

    public function setReclamoProceso(?string $reclamoProceso): self
    {
        $this->reclamoProceso = $reclamoProceso;

        return $this;
    }

    public function getAjuste(): ?string
    {
        return $this->ajuste;
    }

    public function setAjuste(?string $ajuste): self
    {
        $this->ajuste = $ajuste;

        return $this;
    }

    public function getCancelado(): ?string
    {
        return $this->cancelado;
    }

    public function setCancelado(?string $cancelado): self
    {
        $this->cancelado = $cancelado;

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

    public function getMenores(): ?string
    {
        return $this->menores;
    }

    public function setMenores(?string $menores): self
    {
        $this->menores = $menores;

        return $this;
    }

    public function getExcedente(): ?string
    {
        return $this->excedente;
    }

    public function setExcedente(?string $excedente): self
    {
        $this->excedente = $excedente;

        return $this;
    }

    public function getEstimado(): ?string
    {
        return $this->estimado;
    }

    public function setEstimado(?string $estimado): self
    {
        $this->estimado = $estimado;

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

    public function getFechaAsignacion(): ?\DateTimeInterface
    {
        return $this->fechaAsignacion;
    }

    public function setFechaAsignacion(?\DateTimeInterface $fechaAsignacion): self
    {
        $this->fechaAsignacion = $fechaAsignacion;

        return $this;
    }

    public function getFechaDocumentacion(): ?\DateTimeInterface
    {
        return $this->fechaDocumentacion;
    }

    public function setFechaDocumentacion(?\DateTimeInterface $fechaDocumentacion): self
    {
        $this->fechaDocumentacion = $fechaDocumentacion;

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

    public function getFechaRespuesta(): ?\DateTimeInterface
    {
        return $this->fechaRespuesta;
    }

    public function setFechaRespuesta(?\DateTimeInterface $fechaRespuesta): self
    {
        $this->fechaRespuesta = $fechaRespuesta;

        return $this;
    }

    public function getFechaAviso(): ?\DateTimeInterface
    {
        return $this->fechaAviso;
    }

    public function setFechaAviso(?\DateTimeInterface $fechaAviso): self
    {
        $this->fechaAviso = $fechaAviso;

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

    public function getEstatus(): ?string
    {
        return $this->estatus;
    }

    public function setEstatus(?string $estatus): self
    {
        $this->estatus = $estatus;

        return $this;
    }

    public function getTipoMaterial(): ?string
    {
        return $this->tipoMaterial;
    }

    public function setTipoMaterial(?string $tipoMaterial): self
    {
        $this->tipoMaterial = $tipoMaterial;

        return $this;
    }

    public function getEscalado(): ?string
    {
        return $this->escalado;
    }

    public function setEscalado(?string $escalado): self
    {
        $this->escalado = $escalado;

        return $this;
    }

    public function getArea(): ?Area
    {
        return $this->area;
    }

    public function setArea(?Area $area): self
    {
        $this->area = $area;

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

    public function getProveedor(): ?Proveedor
    {
        return $this->proveedor;
    }

    public function setProveedor(?Proveedor $proveedor): self
    {
        $this->proveedor = $proveedor;

        return $this;
    }

    public function getRuta(): ?Ruta
    {
        return $this->ruta;
    }

    public function setRuta(?Ruta $ruta): self
    {
        $this->ruta = $ruta;

        return $this;
    }

    public function getCaja(): ?string
    {
        return $this->caja;
    }

    public function setCaja(?string $caja): self
    {
        $this->caja = $caja;

        return $this;
    }

    public function getComentarios(): ?string
    {
        return $this->comentarios;
    }

    public function setComentarios(?string $comentarios): self
    {
        $this->comentarios = $comentarios;

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

    public function getFechaCheque(): ?\DateTimeInterface
    {
        return $this->fechaCheque;
    }

    public function setFechaCheque(?\DateTimeInterface $fechaCheque): self
    {
        $this->fechaCheque = $fechaCheque;

        return $this;
    }

    public function getFormaPago(): ?string
    {
        return $this->formaPago;
    }

    public function setFormaPago(?string $formaPago): self
    {
        $this->formaPago = $formaPago;

        return $this;
    }


    public function getAnoEvento(): ?string
    {
        return $this->anoEvento;
    }

    public function setAnoEvento(?string $anoEvento): self
    {
        $this->anoEvento = $anoEvento;

        return $this;
    }

    public function getAnoAsignacion(): ?string
    {
        return $this->anoAsignacion;
    }

    public function setAnoAsignacion(?string $anoAsignacion): self
    {
        $this->anoAsignacion = $anoAsignacion;

        return $this;
    }

    public function getAnoDocumentacion(): ?string
    {
        return $this->anoDocumentacion;
    }

    public function setAnoDocumentacion(?string $anoDocumentacion): self
    {
        $this->anoDocumentacion = $anoDocumentacion;

        return $this;
    }


}
