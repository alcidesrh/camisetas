<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ApiResource()
 */
class TallaVenta implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     * @Groups("read_venta")
     */
    private $vendidas = 0;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     * @Groups("read_venta")
     */
    private $cantidad = 0;

    /**
     * @Groups("read_venta")
     */
    private $stock;

    /**
 * @ORM\ManyToOne(targetEntity="App\Entity\Talla")
 * @ORM\JoinColumn(name="talla_id", referencedColumnName="id")
 * @Groups("read_venta")
 */
    private $talla;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TallaStock")
     * @ORM\JoinColumn(name="talla_stock_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $tallaStock;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductoVenta", inversedBy="tallas")
     * @ORM\JoinColumn(name="producto_venta_id", referencedColumnName="id")
     *
     */
    private $producto;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime
     * @Groups("read_venta")
     *
     */
    private $lastUpdate;

    public function __construct(Talla $talla = null)
    {
        $this->talla = $talla;
        $this->vendidas = 0;

    }

    public function getStock(){
        return $this->cantidad + $this->vendidas;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getVendidas()
    {
        return $this->vendidas;
    }

    /**
     * @param mixed $vendidas
     */
    public function setVendidas($vendidas): void
    {
        $this->cantidad -= $vendidas;
        $this->vendidas += $vendidas;
    }



    public function getCantidad()
    {
        return $this->cantidad;
    }

    public function setCantidad($cantidad): self
    {
        $this->cantidad = $cantidad ?? 0;

        return $this;
    }

    public function getTalla()
    {
        return $this->talla;
    }

    public function setTalla(Talla $talla): self
    {
        $this->talla = $talla;

        return $this;
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

    /**
     * @return \DateTime
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * @param \DateTime $lastUpdate
     */
    public function setLastUpdate(\DateTime $lastUpdate): void
    {
        $this->lastUpdate = $lastUpdate;
    }

    /**
     * @return mixed
     */
    public function getTallaStock()
    {
        return $this->tallaStock;
    }

    /**
     * @param mixed $tallaStock
     */
    public function setTallaStock($tallaStock): void
    {
        $this->tallaStock = $tallaStock;
    }



    public function jsonSerialize(): array
    {
        return [ 'id' => $this->id, 'talla' => $this->talla->getNombre(), 'stock' => $this->cantidad ];
    }
}
