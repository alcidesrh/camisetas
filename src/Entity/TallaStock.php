<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductoTallaStockRepository")
 * @ApiResource()
 */
class TallaStock implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     * @Groups("read_pedido")
     */
    private $vendidas = 0;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     * @Groups("read_pedido")
     */
    private $cantidad = 0;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Talla")
     * @ORM\JoinColumn(name="talla_id", referencedColumnName="id")
     * @Groups("read_pedido")
     */
    private $talla;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductoPedido", inversedBy="tallas")
     * @ORM\JoinColumn(name="producto_pedido_id", referencedColumnName="id")
     *
     */
    private $producto;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime
     * @Groups("read_pedido")
     *
     */
    private $lastUpdate;

    public function __construct(Talla $talla = null)
    {
        $this->talla = $talla;
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
        $this->vendidas = $vendidas;
    }



    public function getCantidad()
    {
        return $this->cantidad;
    }

    public function setCantidad(?int $cantidad): self
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

    public function jsonSerialize(): array
    {
        return [ 'id' => $this->id, 'talla' => $this->talla->getNombre(), 'stock' => $this->cantidad ];
    }
}
