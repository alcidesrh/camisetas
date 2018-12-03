<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity()
 * @ORM\Entity(repositoryClass="App\Repository\ProductoStockRepository")
 * @ApiResource()
 */
class ProductoStock implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("read_stock")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Producto")
     * @ORM\JoinColumn(name="producto_id", referencedColumnName="id")
     * @Groups("read_stock")
     */
    private $producto;

    /**
     *@ORM\OneToMany(targetEntity="App\Entity\TallaStock", mappedBy="producto", cascade={"persist", "remove"})
     *@Groups("read_stock")
     */

    private $tallas;

    /**
     * Many Features have One Product.
     * @ORM\ManyToOne(targetEntity="App\Entity\Stock", inversedBy="productos", cascade={"persist"})
     * @ORM\JoinColumn(name="stock_id", referencedColumnName="id")
     */
    private $stock;


    private $venta;


    public function getStock(){
        $cant = 0;
        foreach ($this->tallas as $talla)
            $cant += $talla->getCantidad();
        return $cant;
    }

    public function getVenta(){
        $cant = 0;
        foreach ($this->tallas as $talla)
            $cant += $talla->getVendidas();
        return $cant;
    }


    public function __construct() {
        $this->tallas = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getProducto()
    {
        return $this->producto;
    }

    /**
     * @param mixed $producto
     */
    public function setProducto($producto): void
    {
        $this->producto = $producto;
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
    public function getTalla($index)
    {
        return $this->tallas->get($index);
    }

    /**
     * @param mixed $stock
     */
    public function setStock($stock): void
    {
        $this->stock = $stock;
    }

    public function jsonSerialize(): array
    {
        return [ 'id' => $this->id, 'producto' => $this->producto, 'tallas' => $this->tallas->toArray() ];
    }

}
