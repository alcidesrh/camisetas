<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\TallaRepository")
 */
class Talla
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("read_pedido")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Groups("read_pedido")
     */

    private $nombre;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = strtoupper($nombre);
    }

    /**
     * @return string
     */
    public function getNombre(): string
    {
        return $this->nombre;
    }


}
