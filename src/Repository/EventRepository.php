<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    // /**
    //  * @return Event[] Returns an array of Event objects
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
    public function findOneBySomeField($value): ?Event
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getEventByPlanning($id)
    {
        return $this->createQueryBuilder('s')
            ->join('s.planning', 'c')
            ->addSelect('c')
            ->where('c.id=:id')
            ->setParameter('id',$id)
            ->getQuery()
            ->getResult();
    }

    public function SortByNameEvent(){
        return $this->createQueryBuilder('e')
            ->orderBy('e.category','ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function SortByPriceEvent()
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.fees','ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    public function SortByParticipants(){
        return $this->createQueryBuilder('e')
            ->orderBy('e.nombreReservation','ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    public function findByNameEvent( $NameEvent)
    {
        return $this-> createQueryBuilder('e')
            ->andWhere('e.category LIKE :NameEvent')
            ->setParameter('NameEvent','%' .$NameEvent. '%')
            ->getQuery()
            ->execute();
    }
    public function findByPlaceEvent( $PlaceEvent)
    {
        return $this-> createQueryBuilder('e')
            ->andWhere('e.PlaceEvent LIKE :PlaceEvent')
            ->setParameter('PlaceEvent','%' .$PlaceEvent. '%')
            ->getQuery()
            ->execute();
    }
    public function findByDateDebut( $DateDebut)
    {
        return $this-> createQueryBuilder('e')
            ->andWhere('e.startDate LIKE :DateDebut')
            ->setParameter('DateDebut','%' .$DateDebut. '%')
            ->getQuery()
            ->execute();
    }
    public function findByDateFin( $DateFin)
    {
        return $this-> createQueryBuilder('e')
            ->andWhere('e.endDate LIKE :DateFin')
            ->setParameter('DateFin','%' .$DateFin. '%')
            ->getQuery()
            ->execute();
    }

}
