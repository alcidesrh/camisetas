<?php

namespace App\Repository;

use App\Entity\TallaStock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TallaStock|null find($id, $lockMode = null, $lockVersion = null)
 * @method TallaStock|null findOneBy(array $criteria, array $orderBy = null)
 * @method TallaStock[]    findAll()
 * @method TallaStock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductoTallaStockRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TallaStock::class);
    }

//    /**
//     * @return ProductoTallaStock[] Returns an array of ProductoTallaStock objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProductoTallaStock
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
