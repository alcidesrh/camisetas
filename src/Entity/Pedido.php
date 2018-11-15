<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource( attributes={"order"={"createAt": "DESC"}}, normalizationContext={"groups"={"read_pedido"}})
 * @ORM\Entity(repositoryClass="App\Repository\PedidoRepository")
 */
class Pedido implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("read_pedido")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="pedidos", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @Groups("read_pedido")
     */
    private $user;


    /**
     *@ORM\OneToMany(targetEntity="App\Entity\ProductoPedido", mappedBy="pedido", cascade={"persist", "remove"}, orphanRemoval=true)
     *@Groups("read_pedido")
     */
    private $productos;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @Assert\DateTime
     * @Groups("read_pedido")
     *
     */
    private $createAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime
     *
     */
    private $lastUpdate;

    /**
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $active;

    /**
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $close;

    /**
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $edited;


    /**
     * @Groups("read_pedido")
     */
    private $stock = 0;

    /**
     * @Groups("read_pedido")
     */
    private $venta = 0;


    public function getStock(){
        $cant = 0;
        foreach ($this->productos as $producto)
            $cant += $producto->getStock();
        return $cant;
    }

    public function getVenta(){
        $cant = 0;
        foreach ($this->productos as $producto)
            $cant += $producto->getVenta();
        return $cant;
    }

    public function __construct()
    {
        $this->productos = new ArrayCollection();
        $this->createAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): self
    {
        $this->user = $user;
        return $this;
    }

    public function addProductos( ProductoPedido ...$productos ): void
    {
        foreach ( $productos as $producto ) {
            if ( !$this->productos->contains( $producto ) ) {
                $this->productos->add( $producto );
            }
        }
    }

    public function removeProduct(ProductoPedido $productoPedido){
        unset($this->productos[$this->productos->indexOf($productoPedido)]);
    }

    public function removeProducto($producto)
    {
        $this->productos->removeElement($producto);
    }

    public function getProductos( )
    {
        return $this->productos;
    }



    /**
     * @return \DateTime
     */
    public function getCreateAt(): \DateTime
    {
        return $this->createAt;
    }

    /**
     * @param \DateTime $createAt
     */
    public function setCreateAt(\DateTime $createAt): void
    {
        $this->createAt = $createAt;
    }

    /**
     * @return \DateTime
     */
    public function getLastUpdate(): \DateTime
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
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active): void
    {
        $this->active = $active;
    }

    /**
     * @return mixed
     */
    public function getClose()
    {
        return $this->close;
    }

    /**
     * @param mixed $close
     */
    public function setClose($close): void
    {
        $this->close = $close;
        $this->active = $close;
    }

    /**
     * @return mixed
     */
    public function getEdited()
    {
        return $this->edited;
    }

    /**
     * @param mixed $edited
     */
    public function setEdited($edited): void
    {
        $this->edited = $edited;
    }

    public function jsonSerialize(): array
    {
        return [ 'id' => $this->id, 'productos' => $this->productos->toArray() ];
    }

}
