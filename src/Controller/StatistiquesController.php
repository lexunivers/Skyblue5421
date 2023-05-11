<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use App\Entity\Statistiques;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class StatistiquesController extends CRUDController
{


    public function preList(Request $request): ?Response
    {
        if (false === $this->admin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        // A - On va chercher toutes les Users
        $em = $this->getDoctrine()->getManager();
        $Users = $em->getRepository('App\Entity\User')->findAll();

       // $Compta = $em->getRepository('App\Entity\OperationComptable')->findBySommeTotaleRecette();

      
        // A - 1 On "démonte" les données pour les séparer tel qu'attendu par ChartJS		
        $UserNom = [];
        $UserColor = [];
        $UserCount = [];		
		
        foreach($Users as $User){
            $UserNom[] = $User->getFirstname();
           // $categColor[] = $User->getColor();
           // $UserCount[] = count(is_countable($User)?$UserNom:[]);
            $UserCount[] = count(array($User));
        }

        // B - On va chercher le nombre de reservation par date
			$reservations = $em->getRepository('App\Entity\Reservation')->countByDate();

			// B - 1 On "démonte" les données pour les séparer tel qu'attendu par ChartJS
			$dates = [];
			$reservationsCount = []; 

			foreach($reservations as $reservation){
				$dates[] = $reservation['jour'];
				$reservationsCount[] = $reservation['nombre'] ;
			}

        return $this->render('Statistiques/index.html.twig', [
            'UserNom' => json_encode($UserNom),
            'UserColor' => json_encode($UserColor),
            'UserCount' => json_encode($UserCount),
            'dates' => json_encode($dates),
            'reservationsCount' => json_encode($reservationsCount),
        ])
        ;

        //return $this->render('/Admin/CpteIndividuel/cpte_solo.html.twig',     
       // return $this->render('statistiques/index.html.twig');
    }


}
