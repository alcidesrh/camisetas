<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;


use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;;

use App\Entity\AbstractClasses\NombreAbstract;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ApiResource(
 *     attributes={"order"={"nombre": "ASC"},"normalization_context"={"groups"={"read"}}}
 *     )
 * @ApiFilter(BooleanFilter::class, properties={"stock"})
 */
class User extends NombreAbstract implements UserInterface, \Serializable, \JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read", "read_stock", "read_venta"})
     */
    private $id;

    private $apiToken;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"read"})
     */
    protected $nombre;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank()
     * @Groups({"read"})
     */
    private $apellidos;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=50)
     * @Groups({"read"})
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     *
     */
    private $password;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     * @Groups({"read"})
     */
    private $roles = [];

    /**
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Stock", mappedBy="user", cascade={"persist", "remove"})
     */
    private $stock;

    /**
     * @Groups({"read", "read_stock", "read_venta"})
     */
    private $fullName;

    /**
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Venta", mappedBy="user", cascade={"persist", "remove"})
     */
    private $ventas;

    public function __construct()
    {
        $this->ventas = new ArrayCollection();
    }

    public function getStock(){
        return $this->stock;
    }


    public function getFullName(){
        return $this->nombre.' '. $this->apellidos;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * @param mixed $apiToken
     */
    public function setApiToken($apiToken): void
    {
        $this->apiToken = $apiToken;
    }


    /**
     * @return string
     */
    public function getApellidos(): string
    {
        return $this->apellidos;
    }

    /**
     * @param string $apellidos
     */
    public function setApellidos(string $apellidos): void
    {
        $this->apellidos = $apellidos;
    }



    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * Returns the roles or permissions granted to the user for security.
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        // guarantees that a user always has at least one role for security
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        // See "Do you need to use a Salt?" at https://symfony.com/doc/current/cookbook/security/entity_provider.html
        // we're using bcrypt in security.yml to encode the password, so
        // the salt value is built-in and you don't have to generate one

        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
        // if you had a plainPassword property, you'd nullify it here
        // $this->plainPassword = null;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        return serialize([$this->id, $this->username, $this->password]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        [$this->id, $this->username, $this->password] = unserialize($serialized, ['allowed_classes' => false]);
    }

    public function jsonSerialize(): array
    {
        return [ 'id' => $this->id, 'nombre' => $this->nombre, 'username' => $this->username, 'apellidos' => $this->apellidos ];
    }
}
