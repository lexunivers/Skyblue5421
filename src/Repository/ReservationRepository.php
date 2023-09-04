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

// modéle pour éviter chevauchement
//    SELECT
//    COUNT(*) AS compteur
//FROM
//            reservations
//WHERE
//        :dateArrivee BETWEEN date_arrivee AND date_depart
//    OR  :dateDepart BETWEEN date_arrivee AND date_depart
//    OR  :dateArrivee < date_arrivee
//        AND :dateDepart > date_depart

public function myfindNumeroOrdreId($NumeroOrdre){
    return $this->createQueryBuilder('r')
    ->select('r.id','r.NumeroOrdre','r.realisation','r.reservataire')
    //->where('r.NumeroOrdre =:NumeroOrdre')
    ->where('r.NumeroOrdre = :NumeroOrdre')
    //->andWhere('realisation = false')
    ->setParameter( 'NumeroOrdre', $NumeroOrdre)
    //->setParameter('r.realisation', true)
    //->andWhere('r.NumeroOrdre =:NumeroOrdre')
    //->setParameter('r.realisation', true)
    ->getQuery()
    ->getResult()
    ;    
}

public function myfindOrdreId($NumeroOrdre){
    return $this->createQueryBuilder('r')
    ->select('r.id','r.NumeroOrdre','r.realisation')
    ->where('r.id =:id')
    //->andWhere('r.realisation =false')
    //->setParameter('realisation', true)
    ->setParameter( 'id', $NumeroOrdre)
    ->getQuery()
    ->getResult()
    ;    
}

public function findByCodeReservation($reservataire)
{
    return $this->createQueryBuilder('r')
    ->select('r.CodeReservation', 'r.title')
    //->innerJoin('App\Entity\Vol','v')
    //->where('r.CodeReservation =:CodeReservation')
    ->setParameter('CodeReservation',$reservataire)        
    ->getQuery()
    ->getResult()
    ;
}
//SELECT `NumeroOrdre`as NumeroR, CodeReservation as CodeV, reservataire as User FROM reservation, vol V WHERE CodeReservation = 4682; 
//SELECT `NumeroOrdre`as NumeroR, CodeReservation as CodeV, reservataire as User FROM reservation, vol V WHERE CodeReservation = 483 AND reservataire = 8; 
    

    public function myfindReserv($id)
    {
    return $this->createQueryBuilder('i')
        ->select('i.id, i.NumeroOrdre, i.reservataire')
        //->innerJoin('CodeReservation', 'V')
        //->addSelect('V CodeReservation')
        //->andWhere('i.NumeroOrdre = V.CodeReservation')
        ->andWhere('i.id = :id')
        ->setParameter('id', $id)
		->getQuery()
        ->getResult()
		;        
    }

    public function myfindRealisation($CodeReservation)
    {
		return $this->createQueryBuilder('r')
            ->select('r.NumeroOrdre, r.reservataire, r.id, r.title, r.realisation')
            
            ->addSelect('Vol','V')
            ->innerJoin('CodeReservation','V')
            ->where('r.id = V.CodeReservation')
           // >setParameter('NumeroOrdre', $CodeReservation)
            // ->addSelect('user') 
           // ->andWhere('CodeReservation V = NumeroOrdre')
           // ->andWhere('r.id =:NumeroOrdre')
            //->andWhere('r.NumeroOrdre = :NumeroOrdre')
            //->setParameter('r.id =:?id')
            //->setParameter('id = $id')
            //->setParameter('r.realisation', '1-r.realisation' )

            



        //->from('App\Entity\Reservation', 'V')
        //->addSelect('V.CodeReservation')
        //->addSelect('V.reservataire')


        //->Where('r.CodeReservation = V.CodeReservation ')
        //->andWhere('r.reservataire = : V.reservataire')
        //->andWhere('O.CompteId = :V.id')
        //->setParameter('V.id', $vol)
		//->setParameter('r.id',$id)
		->getQuery()
        ->getResult()
		;

    }

    public function myfindCode($CodeReservation)
	{
		return $this->createQueryBuilder('r')
		->select('r.id','r.reservataire','r.CodeReservation')
		->where('r.CodeReservation = :CodeReservation ')
		->setParameter('CodeReservation',$CodeReservation)
		->getQuery()
        ->getResult()
		;
	}

    public function myfindCodeReservation($CodeReservation)
	{
		return $this->createQueryBuilder('r')
		->select('r.id','r.reservataire','r.CodeReservation')
		->where('r.id = :CodeReservation')
		->setParameter('CodeReservation',$CodeReservation)
		->getQuery()
        ->getResult()
		;
	}

    //SELECT (v.CodeReservation) as CodeDsVol, (v.User_id) as Pilote,(r.CodeReservation) as CodeDsReservation FROM `vol`v, reservation r WHERE v.CodeReservation = r.CodeReservation;     
//SELECT (v.id) as RandDsVol, (v.CodeReservation) as CodeDsVol, (v.User_id) as Pilote,(r.NumeroOrdre) as NumeroDsReservation, (r.id) as idDansReservation FROM `vol`v, reservation r WHERE v.CodeReservation = r.NumeroOrdre; 
  
    public function myfindCodeR($reservataire)
	{

        $qb = $this->createQueryBuilder('r');
         
        $qb->where('r.reservataire = :reservataire')
           ->setParameter('reservataire', $reservataire);
        return $qb;
		
	}
    

    public function myfindPetitCodeR($reservataire)
	{    
		return $this->createQueryBuilder('r')
        ->select('r.NumeroOrdre, r.id , r.reservataire, r.realisation')
        ->where('r.reservataire =:reservataire')
       // ->andWhere('r.NumeroOrdre =:CodeReservation')
		->setParameter('reservataire',$reservataire)        
		->getQuery()
        ->getResult()
		;
	}    


    //SELECT formateur as formateur, title as title FROM `reservation` WHERE `reservataire` = `reservataire`; 
	public function myReservataire()
	{
		return $this->createQueryBuilder('r')
		->select('r.reservataire')
		->where('r.reservataire = 1 OR r.reservataire = 8 ')
        //->andwhere('r.reservataire = 1')
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
