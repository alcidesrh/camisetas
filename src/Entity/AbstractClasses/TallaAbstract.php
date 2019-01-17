<?php

namespace App\Entity\AbstractClasses;

use App\Entity\Talla;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass()
 */
abstract class TallaAbstract
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read_venta"})
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     * @Groups({"read_stock", "read_venta"})
     */
    protected $vendidas = 0;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     * @Groups({"read_stock", "read_venta"})
     */
    protected $cantidad = 0;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Talla")
     * @ORM\JoinColumn(name="talla_id", referencedColumnName="id")
     * @Groups({"read_stock", "read_venta"})
     */
    protected $talla;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime
     * @Groups({"read_stock", "read_venta"})
     *
     */
    protected $lastUpdate;


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
        if($vendidas >= $this->cantidad){
            $this->vendidas += $this->cantidad;
            $this->cantidad = 0;
            return;
        }
        $this->cantidad -= $vendidas;
        $this->vendidas += $vendidas;
    }

    public function returnProduct($cant): void
    {
        if($cant >= $this->vendidas){
            $this->cantidad += $this->vendidas;
            $this->vendidas = 0;
            return;
        }
        $this->cantidad += $cant;
        $this->vendidas -= $cant;
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

}
