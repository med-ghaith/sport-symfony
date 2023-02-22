<?php

namespace App\Repository;

use App\Entity\Exercice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Exercice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Exercice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Exercice[]    findAll()
 * @method Exercice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExerciceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exercice::class);
    }

    /**
     * @return Exercice[] Returns an array of Exercice objects
     */
    public function findByEquipments($value, $value2, $value3): array
    {
        if ($value && $value2 == null && $value3 == null) {
            return $this->createQueryBuilder('e')
                ->join('e.equipments', 'q')
                ->addSelect('q')
                ->where('q.name = :val')
                ->setParameter('val', $value)
                ->setMaxResults(10)
                ->getQuery()
                ->getResult();
        } else if ($value && $value2 && $value3 == null) {
            return $this->createQueryBuilder('e')
                ->join('e.equipments', 'q')
                ->addSelect('q')
                ->where('q.name = :val')
                ->orWhere('q.name = :val2')
                ->setParameter('val', $value)
                ->setParameter('val2', $value2)
                ->setMaxResults(10)
                ->getQuery()
                ->getResult();
        } else {
            return $this->createQueryBuilder('e')
                ->join('e.equipments', 'q')
                ->addSelect('q')
                ->where('q.name = :val')
                ->orWhere('q.name = :val2')
                ->orWhere('q.name = :val2')
                ->orWhere('q.name = :val3')
                ->setParameter('val', $value)
                ->setParameter('val2', $value2)
                ->setParameter('val3', $value3)
                ->setMaxResults(10)
                ->getQuery()
                ->getResult();
        }
//        }else{
//            return $this->createQueryBuilder('e')
//                ->andWhere('e.equipment.name = :val')
//                ->setParameter('val', $value)
//                ->setMaxResults(10)
//                ->getQuery()
//                ->getResult();
//        }

    }

    public function countExercice(): int
    {
        return $this->createQueryBuilder('e')
//            ->join('e.equipments', 'q')
//            ->addSelect('q')
//            ->where('q.name = :val')
//            ->setParameter('val', $value)
            ->select('count(e.id)')
            ->getQuery()
            ->getSingleScalarResult();

    }


    // /**
    //  * @return Exercice[] Returns an array of Exercice objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Exercice
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
