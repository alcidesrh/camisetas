<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Filter\PropertyFilter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource( attributes={"order"={"createAt": "DESC"}}, normalizationContext={"groups"={"read_venta"}})
 * @ApiFilter(PropertyFilter::class)
 * @ORM\Entity(repositoryClass="App\Repository\VentaRepository")
 */
class Venta implements JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("read_venta")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("read_venta")
     */
    private $feria;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @Assert\DateTime
     * @Groups("read_venta")
     *
     */
    private $createAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime
     * @Groups("read_venta")
     *
     */
    private $lastUpdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime
     * @Groups("read_venta")
     *
     */
    private $closeAt;

    /**
     *
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups("read_venta")
     */
    private $open;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="ventas", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @Groups("read_venta")
     */
    private $user;

    /**
     *@ORM\OneToMany(targetEntity="App\Entity\ProductoVenta", mappedBy="venta", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Groups("read_venta")
     */
    private $productos;

    /**
     * @Groups("read_venta")
     */
    private $stock = 0;

    /**
     * @Groups("read_venta")
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
        $this->open = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFeria(): ?string
    {
        return $this->feria;
    }

    public function setFeria(?string $feria): self
    {
        $this->feria = $feria;

        return $this;
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
     * @return \DateTime
     */
    public function getCloseAt()
    {
        return $this->closeAt;
    }

    /**
     * @param \DateTime $closeAt
     */
    public function setCloseAt(\DateTime $closeAt): void
    {
        $this->closeAt = $closeAt;
    }

    /**
     * @return mixed
     */
    public function getOpen()
    {
        return $this->open;
    }

    /**
     * @param mixed $open
     */
    public function setOpen($open): void
    {
        $this->open = $open;
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
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getProductos()
    {
        return $this->productos;
    }

    /**
     * @param mixed $productos
     */
    public function setProductos($productos): void
    {
        $this->productos = $productos;
    }

    public function removeProduct(ProductoVenta $productoVenta){
        unset($this->productos[$this->productos->indexOf($productoVenta)]);
    }

    public function getProductoClean()
    {
       $result = [];
       foreach($this->productos as $item){
           $result[] = [
            'producto' => $item->getProducto(), 'tallas' => $item->getTallasClean(), 'sold' => $item->getVenta(), 'stock' => $item->getStock()
           ];
       }
       return $result;
    }
    public function jsonSerialize()
    {
        return [
            'name' => $this->feria,
            'active' => $this->open,
            'productos' => $this->getProductoClean(),
            'sold' => $this->getVenta(),
            'stock' => $this->getStock()
        ];
    }


}
