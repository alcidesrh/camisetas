<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductoTallaStockRepository")
 */
class TallaStock
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $vendidas = 0;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $cantidad = 0;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Talla", cascade={"persist"})
     * @ORM\JoinColumn(name="talla_id", referencedColumnName="id")
     */
    private $talla;

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

}
