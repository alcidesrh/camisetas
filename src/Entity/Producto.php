<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\ProductoRepository")
 */
class Producto
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     *  @Groups("read_pedido")
     */

    private $nombre;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\File", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     * @Groups("read_pedido")
     */
    private $imagen;

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
        return $this->imagen;
    }

    public function setImagen(File $imagen): self
    {
        $this->imagen = $imagen;

        return $this;
    }

//    public function addTallas( Talla ...$tallas ): void
//    {
//        foreach ( $tallas as $talla ) {
//            if ( !$this->tallas->contains( $talla ) ) {
//                $this->tallas->add( $talla );
//            }
//        }
//    }
//
//    public function removeTallas($talla)
//    {
//        $this->tallas->removeElement($talla);
//    }
//
//    public function getTallas( )
//    {
//        return $this->tallas;
//    }
}
