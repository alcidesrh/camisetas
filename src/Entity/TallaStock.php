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
    private $cantidad = 0;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Talla", cascade={"persist"})
     * @ORM\JoinColumn(name="talla_id", referencedColumnName="id")
     */
    private $talla;


    public function getId(): ?int
    {
        return $this->id;
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
