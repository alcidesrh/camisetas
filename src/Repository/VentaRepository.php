<?php

namespace App\Repository;

use App\Entity\ProductoStock;
use App\Entity\TallaStock;
use App\Entity\Venta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Venta|null find($id, $lockMode = null, $lockVersion = null)
 * @method Venta|null findOneBy(array $criteria, array $orderBy = null)
 * @method Venta[]    findAll()
 * @method Venta[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VentaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Venta::class);
    }

//    /**
//     * @return Venta[] Returns an array of Venta objects
//     */

    public function findTallaByTallaStock(TallaStock $talla)
    {
        return $this->getEntityManager()->createQuery('
        SELECT tv FROM App:TallaVenta tv
         JOIN tv.tallaStock ts
         JOIN tv.producto pv
         JOIN pv.venta v
         WHERE v.open = :open AND tv.talla = :talla AND ts = :tallaStock')->setParameters(['open' => true, 'talla' => $talla->getTalla(), 'tallaStock' => $talla])->getOneOrNullResult();

    }
    public function findProductoVentaByProductoStock(ProductoStock $productoStock)
    {
        return $this->getEntityManager()->createQuery('
        SELECT DISTINCT IDENTITY(tv.producto) FROM App:TallaVenta tv
         JOIN tv.tallaStock ts
         WHERE ts.producto = :producto')->setParameter('producto', $productoStock)->getOneOrNullResult();

    }

    /*
    public function findOneBySomeField($value): ?Venta
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
