<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\AbstractClasses\TallaAbstract;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductoTallaStockRepository")
 * @ApiResource()
 */
class TallaStock extends TallaAbstract implements \JsonSerializable
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductoStock", inversedBy="tallas")
     * @ORM\JoinColumn(name="producto_stock_id", referencedColumnName="id")
     *
     */
    protected $producto;

    public function __construct(Talla $talla = null)
    {
        $this->talla = $talla;
        $this->vendidas = 0;

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
        return [ 'id' => $this->id, 'talla' => $this->talla->getNombre(), 'stock' => $this->cantidad ];
    }
}
