<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\AbstractClasses\NombreAbstract;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\ProductoRepository")
 */
class Producto extends NombreAbstract
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\File", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    private $imagen;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\TallaStock", orphanRemoval=true, cascade={"persist", "remove"} )
     * @ORM\JoinTable(name="producto_talla",
     *      joinColumns={@ORM\JoinColumn(name="producto_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="talla_id", referencedColumnName="id", unique=true)})
     */

    private $tallas;

    public function __construct() {
        $this->tallas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function addTallas( TallaStock ...$tallas ): void
    {
        foreach ( $tallas as $talla ) {
            if ( !$this->tallas->contains( $talla ) ) {
                $this->tallas->add( $talla );
            }
        }
    }

    public function removeTallas($talla)
    {
        $this->tallas->removeElement($talla);
    }

    public function getTallas( )
    {
        return $this->tallas;
    }
}
