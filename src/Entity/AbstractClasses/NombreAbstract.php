<?php

namespace App\Entity\AbstractClasses;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 */
abstract class NombreAbstract
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $nombre;

    /**
     * @return string
     */
    public function getNombre(): string
    {
        return $this->nombre;
    }

    /**
     * @param string $nombre
     */
    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

}
