<?php

namespace App\Entity;

use App\Repository\TransportistaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TransportistaRepository::class)
 */
class Transportista
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $transportista;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTransportista(): ?string
    {
        return $this->transportista;
    }

    public function setTransportista(string $transportista): self
    {
        $this->transportista = $transportista;

        return $this;
    }
}
