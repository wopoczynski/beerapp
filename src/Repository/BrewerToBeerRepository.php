<?php

namespace App\Repository;

use App\Entity\BrewerToBeer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BrewerToBeer|null find($id, $lockMode = null, $lockVersion = null)
 * @method BrewerToBeer|null findOneBy(array $criteria, array $orderBy = null)
 * @method BrewerToBeer[]    findAll()
 * @method BrewerToBeer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BrewerToBeerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BrewerToBeer::class);
    }

    // /**
    //  * @return BrewerToBeer[] Returns an array of BrewerToBeer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BrewerToBeer
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
