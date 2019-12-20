<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\AbstractClasses\TallaAbstract;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ApiResource()
 */
class TallaVenta extends TallaAbstract implements \JsonSerializable
{
    /**
     * @Groups("read_venta")
     */
    private $stock;

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
    protected $producto;

    public function __construct(Talla $talla = null)
    {
        $this->talla = $talla;
        $this->vendidas = 0;

    }

    public function getStock(){
        return $this->cantidad + $this->vendidas;
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



    public function jsonSerialize(): array
    {
        return [ 'id' => $this->id, 'talla' => $this->talla->getNombre(), 'stock' => $this->cantidad, 'vendido' => $this->getVendidas() ];
    }
}
