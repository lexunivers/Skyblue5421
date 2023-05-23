<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineExtensions\Query\Mysql;

/**
 * @extends ServiceEntityRepository<Reservation>
 *
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function save(Reservation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Reservation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //SELECT formateur as formateur, title as title FROM `reservation` WHERE `reservataire` = `reservataire`; 
	public function myfindFormateur()
	{
		return $this->createQueryBuilder('r')
		->select('r.formateur, r.title, r.reservataire')
		->where('r.reservataire = r.title')
		//->setParameter('instructeur',$formateur)
		->getQuery()
		->getResult()
		;
	}    

    public function formateur (){
        return $this->createQueryBuilder('r')
        ->select('r.formateur')
        ->where('r.reservataire = r.title ' )
        ->getQuery()
        ->getResult()
        ;
    }          
    
    //SELECT SUBSTRING(created_at, 1, 10) as Debut, SUBSTRING(last_login, 1, 10) as login, Lastname FROM `user`ORDER BY RAND() LIMIT 1; 
    /*
    * code de réservation
    */
   // public function codeReservation()
   // {
     //   return $this->createQueryBuilder('u')        
       // ->select('SUBSTRING(u.start, 1, 10) as Debut, SUBSTRING(u.end, 1, 10) as fin, u.reservataire as Nom')
       // ->addSelect('RAND() as HIDDEN rand')
       // ->orderBy('rand LIMIT 1')
       // ->getQuery()
       // ->getMaxResults(3)
    //;

    //}

    public function codeReservation()
    {
      //  $entityManager = $this->getEntityManager();
      //  $query = $entityManager->createQuery(
      //      "SELECT q  FROM App\Entity\Reservation q order by  RAND()")
      //      ->setMaxResults(3)
      //  ;
        


        return $this->createQueryBuilder('r')
        ->select("SUBSTRING(r.start, 1, 10) as jour, count(r) as nombre  ORDER BY RAND()")
        
        ->setMaxResults(3)
        ->getQuery()
        ->getResult()
        ;
    }    



    /*
    * returns number of "reservation" per day
    */
    public function countByDate()
    {
        return $this->createQueryBuilder('r')
            ->select('SUBSTRING(r.start, 1, 10) as jour, count(r) as nombre ')
            //->select('count(r), nombre')
            ->groupBy('jour')
            ->getQuery()
            ->getResult()
        ;
   // SELECT count(*), SUBSTRING(start, 1, 10) as depart From reservation Group By SUBSTRING(start, 1, 10); 

    }

//    /**
//     * @return Reservation[] Returns an array of Reservation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reservation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
