<?php

namespace App\Repository;

use App\Entity\GymManger;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GymManger|null find($id, $lockMode = null, $lockVersion = null)
 * @method GymManger|null findOneBy(array $criteria, array $orderBy = null)
 * @method GymManger[]    findAll()
 * @method GymManger[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GymMangerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GymManger::class);
    }

    // /**
    //  * @return GymManger[] Returns an array of GymManger objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GymManger
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
