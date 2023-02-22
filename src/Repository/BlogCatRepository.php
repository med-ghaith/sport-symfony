<?php

namespace App\Repository;

use App\Entity\BlogCat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BlogCat|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogCat|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogCat[]    findAll()
 * @method BlogCat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogCatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogCat::class);
    }

    // /**
    //  * @return BlogCat[] Returns an array of BlogCat objects
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
    public function findOneBySomeField($value): ?BlogCat
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
