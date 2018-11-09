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
class Pedido
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("read_pedido")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @Groups("read_pedido")
     */
    private $user;


    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ProductoPedido", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="pedido_producto",
     *      joinColumns={@ORM\JoinColumn(name="pedido_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="producto_pedido_id", referencedColumnName="id", unique=true)}
     *      )
     * @Groups("read_pedido")
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



}
