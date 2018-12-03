<?php

namespace App\Repository;

use App\Entity\ProductoStock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProductoStock|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductoStock|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductoStock[]    findAll()
 * @method ProductoStock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductoStockRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProductoStock::class);
    }

//    /**
//     * @return ProductoStock[] Returns an array of ProductoStock objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProductoStock
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
