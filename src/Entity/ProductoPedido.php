<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity()
 * @ORM\Entity(repositoryClass="App\Repository\ProductoPedidoRepository")
 */
class ProductoPedido
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Producto", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="producto_id", referencedColumnName="id")
     * @Groups("read_pedido")
     */
    private $producto;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\TallaStock", orphanRemoval=true, cascade={"persist", "remove"} )
     * @ORM\JoinTable(name="producto_pedido_talla_stock",
     *      joinColumns={@ORM\JoinColumn(name="producto_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="talla_id", referencedColumnName="id", unique=true)})
     * @Groups("read_pedido")
     */

    private $tallas;


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


}
