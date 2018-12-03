<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Filter\PropertyFilter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource( attributes={"order"={"createAt": "DESC"}}, normalizationContext={"groups"={"read_stock"}})
 * @ApiFilter(PropertyFilter::class)
 * @ORM\Entity(repositoryClass="App\Repository\StockRepository")
 */
class Stock implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("read_stock")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="stock", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * @Groups("read_stock")
     */
    private $user;


    /**
     *@ORM\OneToMany(targetEntity="App\Entity\ProductoStock", mappedBy="stock", cascade={"persist", "remove"}, orphanRemoval=true)
     *@Groups("read_stock")
     */
    private $productos;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @Assert\DateTime
     * @Groups("read_stock")
     *
     */
    private $createAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime
     * @Groups("read_stock")
     *
     */
    private $lastUpdate;

    /**
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $refresh;


    /**
     * @Groups("read_stock")
     */
    private $stock = 0;

    /**
     * @Groups("read_stock")
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
        $this->refresh = true;
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

    public function addProductos( ProductoStock ...$productos ): void
    {
        foreach ( $productos as $producto ) {
            if ( !$this->productos->contains( $producto ) ) {
                $this->productos->add( $producto );
            }
        }
    }

    public function removeProduct(ProductoStock $productoStock){
        unset($this->productos[$this->productos->indexOf($productoStock)]);
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
    public function getRefresh()
    {
        return $this->refresh;
    }

    /**
     * @param mixed $refresh
     */
    public function setRefresh($refresh): void
    {
        $this->refresh = $refresh;
    }




    public function jsonSerialize(): array
    {
        return [ 'id' => $this->id, 'productos' => $this->productos->toArray() ];
    }

}
