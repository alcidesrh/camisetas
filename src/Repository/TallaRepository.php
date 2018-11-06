<?php

namespace App\Repository;

use App\Entity\Talla;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Talla|null find($id, $lockMode = null, $lockVersion = null)
 * @method Talla|null findOneBy(array $criteria, array $orderBy = null)
 * @method Talla[]    findAll()
 * @method Talla[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TallaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Talla::class);
    }

//    /**
//     * @return Talla[] Returns an array of Talla objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Talla
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
