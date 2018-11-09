<?php

namespace App\Repository;

use App\Entity\ProductoPedido;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProductoPedido|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductoPedido|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductoPedido[]    findAll()
 * @method ProductoPedido[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductoPedidoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProductoPedido::class);
    }

//    /**
//     * @return ProductoPedido[] Returns an array of ProductoPedido objects
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
    public function findOneBySomeField($value): ?ProductoPedido
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
