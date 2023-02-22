<?php

namespace App\Repository;

use App\Entity\BlogReview;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BlogReview|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogReview|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogReview[]    findAll()
 * @method BlogReview[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogReview::class);
    }

    // /**
    //  * @return BlogReview[] Returns an array of BlogReview objects
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
    public function findOneBySomeField($value): ?BlogReview
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    

    public function findByName($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.idblog.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
