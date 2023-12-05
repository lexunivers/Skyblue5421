<?php

namespace App\Repository;

use App\Entity\Vol;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method Vol|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vol|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vol[]    findAll()
 * @method Vol[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VolRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vol::class);
    }


    public function myfindMonTemps(){
        return $this->createQueryBuilder('v') 
        ->SELECT('SUM(DATE_DIFF(v.heureDepart, v.heureArrivee)) as SommeDuree')
        //->FROM ('App\Entity\Vol', 'v')
        ->WHERE ('v.typevol = 2')
        ->andWhere(' v.user = 3')
        ->getQuery()
        ->getSingleResult()
        ;
    }        

	



    public function myfindTemps()
	{
    $queryBuilder = $this->createQueryBuilder('v')
                 ->select('SUM(v.heureDepart - v.heureArrivee) as SommeDuree') 
                 //->from('App\Entity\Vol', 'v')
                 ->where('v.typevol = 1')
                 ->andWhere('v.user = 3');

    $sommeDuree = $queryBuilder->getQuery()->getSingleResult();
    return $queryBuilder;
    }

public function myDureeTotaleSolo($user){
    return $this->createQueryBuilder('v')    
    ->Select('SUM(v.heureArrivee - v.heureDepart) as SommeDureeSolo')
    ->where('v.typevol = 1') 
    ->andWhere('v.user =:user')
    ->setParameter('user',$user)
    //->GROUPBY ('DureeDuVol' ) 
    ->getQuery()
    ->getSingleResult(); 
}


public function myDureeTotaleDouble($user){
    return $this->createQueryBuilder('v')
    //->select('v.id, v.typevol, v.heureDepart, v.heureArrivee, v.User_id, date_diff(v.heureDepart, v.heureArrivee) ')
    ->Select('SUM(v.heureArrivee - v.heureDepart) as SommeDureeDouble')
    ->Where('v.typevol = 2')
    ->andWhere('v.user =:user')
    ->setParameter('user',$user)
    //->orderBy('TIMEDIFF(heureDepart, heureArrivee)' )
    //->GROUPBY('SUM(date_DIFF(v.heureDepart,v.heureArrivee) )' )   
    ->getQuery()
    ->getSingleResult()
    
;
}    

public function myDureeTotaleGlobal($user){
    return $this->createQueryBuilder('v')
    //->select('v.id, v.typevol, v.heureDepart, v.heureArrivee, v.User_id, date_diff(v.heureDepart, v.heureArrivee) ')
    ->Select('SUM(v.heureArrivee - v.heureDepart) as SommeDureeGlobale')
    //->Where('v.typevol = 2')
    ->Where('v.user =:user')
    ->setParameter('user',$user)
    //->orderBy('TIMEDIFF(heureDepart, heureArrivee)' )
    //->GROUPBY('SUM(date_DIFF(v.heureDepart,v.heureArrivee) )' )   
    ->getQuery()
    ->getSingleResult()
    
;
}

//$sommeTotale =$em->getRepository('App\Entity\OperationComptable')->myfindSommeTotale($user);

public function myfindSommeTotale($user)
{
    return $this->createQueryBuilder('V')
        ->select('V.CompteId,SUM(V.dureevol)')
        ->Where('v.CompteId = :CompteId')
        ->setParameter('CompteId', $user)
        ->GROUPBY('V.dureevol')
        ->getQuery()
        ->getResult()
    ;
}

public function findByUser($user)
{
    return $this->createQueryBuilder('c')
    ->select('c.CodeReservation','c.user')
    ->innerJoin('App\Entity\Reservation','r')
    ->where('r.user =:user')
    ->setParameter('user',$user)        
    ->getQuery()
    ->getResult()
    ;
}

    /**
     * @return Avion[] Returns an array of Avion objects
    */
    public function findByExampleField()
    {
        return $this->createQueryBuilder('v')
            ->select('v.datevol, v.id, v.heureDepart, v.heureArrivee, r.start, r.end')
            ->innerJoin( 'App\Entity\Reserver', 'r' )
            ->andWhere( 'r.start = v.heureDepart' )            
            ->andWhere('r.end = v.heureArrivee')
			->between('v.heureDepart', 'start', 'end')
            ->groupBy('v.datevol')
            ->orderBy('v.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByExampleBetween()
    {
        return $this->createQueryBuilder('v')
            ->select('v.datevol, v.id, v.heureDepart, v.heureArrivee, r.title, r.start, r.end')
            ->innerJoin( 'App\Entity\Reserver', 'r' )		
			->andWhere(['v.heureDepart', 'BETWEEN', 'r.start','r.end'] )
			//->BetweenColumnsCondition('v.heureDepart', 'BETWEEN', 'r.start', 'r.end')
			//->andWhere('between', 'id', 1, 10 )
			//->setParameter('r.start', start->format('Y-m-d'))
			//->setParameter('r.end', end->format('Y-m-d'))			
            ->orderBy('v.datevol', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
	}	


    // /**
    //  * @return Vol[] Returns an array of Vol objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

	//SELECT `datevol`, `heureDepart`, `heureArrivee`, `validation`, `start`, `end` FROM `vol`, `reserver` WHERE `validation` = 1 GROUP BY `datevol`

    public function findByValidation(): ?Vol
	{
        return $this->createQueryBuilder('v')            
			->select('v.datevol, v.heureDepart, v.heureArrivee, v.validation')
            ->innerJoin( 'App\Entity\Reserver', 'r' )
            ->andWhere( 'r.start = v.heureDepart' )            
            ->andWhere('r.end = v.heureArrivee')
			->andwhere('v.validation = 1')
            ->groupBy('v.datevol')
            ->getQuery()
            ->getResult()
		;
	}

    /*
    public function findOneBySomeField($value): ?Vol
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
