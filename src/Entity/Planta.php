<?php

namespace App\Entity;

use App\Repository\PlantaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlantaRepository::class)
 */
class Planta
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Localidad::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $localidad;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $planta;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPlanta(): ?string
    {
        return $this->planta;
    }

    public function setPlanta(string $planta): self
    {
        $this->planta = $planta;

        return $this;
    }
}
