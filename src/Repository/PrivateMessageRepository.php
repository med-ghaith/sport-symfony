<?php

namespace App\Repository;

use App\Entity\PrivateMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrivateMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrivateMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrivateMessage[]    findAll()
 * @method PrivateMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrivateMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrivateMessage::class);
    }

    /**
     * @return PrivateMessage[]
     * */
    public function showAllUserWithJaw($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.idFirstUser = :val')
            ->orWhere('p.idSecondUser = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return PrivateMessage[]
     * */
    public function findOneByIdUser($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.idFirstUser = :val')
            ->orWhere('p.idSecondUser = :val')
            ->setParameter('val', $value)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return PrivateMessage[]
     * */
    public function findAllMessagesBetweenTwoUsers($firstUser, $secondUser)
    {
        return $this->createQueryBuilder('p')
            ->Where('p.idFirstUser = :fval')
            ->andWhere('p.idSecondUser = :sval')
            ->orWhere('p.idFirstUser = :sval')
            ->andWhere('p.idSecondUser = :fval')
            ->setParameter('fval', $firstUser)
            ->setParameter('sval', $secondUser)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return PrivateMessage[]
     * */
    public function findAllMessagesBetweenTwoUsers2($firstUser, $secondUser)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT p FROM App\Entity\PrivateMessage p WHERE p.idFirstUser=:fval AND p.idSecondUser=:sval OR p.idFirstUser=:sval AND p.idSecondUser=:fval')
            ->setParameter('fval', $firstUser)
            ->setParameter('sval', $secondUser);

        return $query->getResult();
    }

    public function countMessages($month): int
    {
        return $this->createQueryBuilder('e')
            ->where('MONTH(e.createdAt) = :val')//, $month)
            ->setParameter('val', $month)
            ->select('count(e.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // /**
    //  * @return PrivateMessage[] Returns an array of PrivateMessage objects
    //  */
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
    public function findOneBySomeField($value): ?PrivateMessage
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
