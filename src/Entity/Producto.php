<?php

namespace App\Entity;


use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(attributes={"order"={"nombre": "ASC"}})
 * @ORM\Entity(repositoryClass="App\Repository\ProductoRepository")
 * @ApiFilter(ExistsFilter::class, properties={"sudadera"})
 */
class Producto implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read_stock", "read_venta"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"read_stock", "read_venta"})
     */

    private $nombre;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\File", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     * @Groups({"read_stock", "read_venta"})
     */
    private $imagen;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"read_stock", "read_venta"})
     */
    private $sudadera;

    public function __construct() {
        $this->tallas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getNombre(): string
    {
        return $this->nombre;
    }

    /**
     * @param string $nombre
     */
    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getImagen(): ?File
    {
        if($this->imagen)
        return $this->imagen;
        $noImagen = new File();
        $noImagen->setName('No imagen')->setExt('jpg')->setPath('Sin_imagen_disponible.jpg');
        return$noImagen;
    }

    public function setImagen(File $imagen): self
    {
        $this->imagen = $imagen;

        return $this;
    }


    public function jsonSerialize(): array
    {
        return [ 'id' => $this->id, 'nombre' => $this->nombre, 'imagen' => $this->getImagen()->getPath() ];
    }

    public function getSudadera(): ?bool
    {
        return $this->sudadera;
    }

    public function setSudadera(?bool $sudadera): self
    {
        $this->sudadera = $sudadera;

        return $this;
    }
}
