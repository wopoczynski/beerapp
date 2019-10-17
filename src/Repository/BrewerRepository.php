<?php

namespace App\Repository;

use App\Entity\Brewer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Brewer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Brewer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Brewer[]    findAll()
 * @method Brewer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BrewerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Brewer::class);
    }

    // /**
    //  * @return Brewer[] Returns an array of Brewer objects
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
    public function findOneBySomeField($value): ?Brewer
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
