<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Repository\InstructeurRepository;
use App\Entity\Avions;
use App\Entity\Vol;
use App\Entity\User;
use App\Repository\VolRepository;
use App\Repository\AvionsRepository;
use App\Entity\Resources;
use App\Entity\Instructeur;
use App\Form\InstructeurType;
use App\Form\ReservationEditType;
Use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Serializer\SerializerInterface;
use DoctrineExtensions\Query\Mysql;
use Doctrine\ORM;

class ReservationController extends AbstractController
{
    /**
     * @Route("/reservation", name="reservation_index", methods={"GET"})
     */
    public function index(ReservationRepository $reservationRepository): Response
    {
		$editMode = false;
		$reservation = new Reservation();
        $form = $this->createForm(ReservationEditType::class, $reservation);		
        	        
		return $this->render('reservation/index.html.twig', [
		    'editMode' => $editMode,
            'form' => $form->createView(),
            'reservation' => $reservationRepository->findAll(),
        ]);
    }

    /**
    * @Route("/reservation/load", name="reservation_load")
    */
    public function load()
    {
        $em = $this->getDoctrine()->getManager();       
        $liste = $em->getRepository('App\Entity\Reservation')->findAll();

        $serializer = $this->get('serializer');     
        $json = $serializer->serialize($liste, 'json');

        return new Response($json); 
    }
    
    /**
    * @Route("/reservation/avion", name="reservation_avion")
    */
    public function avion()
    {
        $em = $this->getDoctrine()->getManager();       
		$avion = $em->getRepository('App\Entity\Avions')->findAll();//myfindCalendar();

        $serializer = $this->get('serializer');     
        $json = $serializer->serialize($avion, 'json');
	
        return new Response($json); 
    }

    /**
    * @Route("/reservation/resources", name="reservation_resources")
    */
    public function resources()
    {
        $em = $this->getDoctrine()->getManager();       
		$resources = $em->getRepository('App\Entity\Resources')->findAll();

        $serializer = $this->get('serializer');     
        $json = $serializer->serialize($resources, 'json');
	
        return new Response($json); 
    }
 
    /**
    * @Route("/reservation/instructeur", name="reservation_instructeur", requirements={"editMode" = "1"}, methods={"GET","POST"})
    */
    public function instructeur(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();       
		$instructeur = $em->getRepository('App\Entity\Instructeur')->findAll();

        $serializer = $this->get('serializer');     
        $json = $serializer->serialize($instructeur, 'json');
	
        return new Response($json);		
	}
    
    /**
     * @Route("/new", name="reservation_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
		// attributs de session
        $session = $this->get("session");
        $auteur = $this->getUser('session')->getId();
        $user = $this->getUser('session')->getUsername();
				
        if($request->isXmlHttpRequest()) {
            
            $id = $request->get('id');            
               
            // On instancie une nouvelle reservation
            $reservation = new Reservation();

            $title = $request->get('user');
            $start = $request->get('start');
            $end = $request->get('end');
            $resourceId = $request->get('resourceId');
            $formateur = $request->get('formateur');
            $instructeur = $request->get('instructeur');
            $avion = $request->get('avion');
            $appareil = $request->get('appareil');
            //$user = $request->get('title'); 
            $em = $this->getDoctrine()->getManager();
            
            // Une réservation est faite:
                // a - soit par le Pilote lui-même dans son espace,
                // b - soit par le club
            // Si la réservation est faite par le Club, il faut changer "admin" par le nom du pilote
            
            if($user == "admin"){

              $user = $_SESSION['user'];
              $nom = $_SESSION['nom'];
              $prenom = $_SESSION['prenom'];
             // $editMode = $_SESSION['editMode'];
              $instructeur = $_SESSION['instructeur'];    
              $user = $prenom;              
            }
			
            // on vérifie s'il y a un instructeur
            if ($instructeur == false ){               
			    $reservation->setTitle($user);
                $reservation->setFormateur("Néant");
                $reservation->setUser($title);          

                $appareil = $em->getRepository('App\Entity\Resources')->myfindAvion($resourceId);           
                

                foreach ($appareil as $value) {
					$value;
				};
                $appareil = implode("--", $value);
                $reservation->setAppareil($appareil);              
             
            }else{

               // $em = $this->getDoctrine()->getManager();
                $formateur = $em->getRepository('App\Entity\Instructeur')->myfindNom($instructeur);              		
				$initiale = $em->getRepository('App\Entity\Instructeur')->myfindInitiales($instructeur);				
                $appareil = $em->getRepository('App\Entity\Resources')->myfindAvion($resourceId);

                // - 1 - On renseigne l'attribut 'Appareil'
                foreach ($appareil as $value1) {
					$value1;
				};
                $appareil = implode("--", $value1);
                $reservation->setAppareil($appareil);

                // - 2 - On renseigne l'attribut 'formateur'
                foreach ($formateur as $value2) {
					$value2;
				};
                $nom = implode(":", $value2);                 
                $reservation->setFormateur($nom);

                // - 3 - On renseigne l'attribut 'Initiales' Instructeur
                foreach ($initiale as $value3) {
					$value3;
				};

				$title	= $user." / ".$value3;              
				$reservation->setTitle($title);
			}
                // - 4 - on attribue code Reservation
                $CodeReservation = rand(0,10000);
                //echo"code".$CodeReservation;
                //exit;

			$form = $this->createForm(ReservationEditType::class, $reservation);
			$form->handleRequest($request);

            $reservation->setStart(new \DateTime($start) );
            $reservation->setEnd(new \DateTime($end) );
            $reservation->setResourceId($resourceId);
            $reservation->setReservataire($auteur );
            $reservation->setCodeReservation($CodeReservation);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reservation);
            $entityManager->flush();
        }

        //$response = new Response(json_encode($stop));
        //$response->headers->set('Content-Type', 'application/json');
        //return $response;
       // $form = $this->createForm(ReservationType::class, $reservation);
        //$form->handleRequest($request);				

            return $this->render('reservation_admin/index.html.twig', [
               // 'editMode' => $editMode,
                'form' => $form->createView(),
                'user'=>$user,
            ]);        
    }
    
    /**
     * @Route("/update", name="reservation_update", methods={"GET","POST"})
     */
    public function update(Request $request): Response
    {

		// attributs de session
        $user = $this->container->get('security.token_storage')->getToken()->getUser()->getId();

        if($request->isXmlHttpRequest()) {
            
            $id = $request->get('id');

            // Récupère l'objet en fonction de l'@Id
            $repository = $this->getDoctrine()->getRepository(Reservation::class);   
            $reservation = $repository->find($id);
            $reservataire = $reservation->getReservataire($id);
			$instructeur = $reservation->getInstructeur($id);
 
            // Seul le réservataire ( le User d'origine) peut modifier sa réservation 
            if ($user !== $reservataire)   
			{
                $stop = true;
            }else{
                $stop = false;
				
                //$title = $request->get($title);
                $start = $request->get('start');
                $end = $request->get('end');
                $resourceId = $request->get('resourceId');

                $reservation->setStart(new \DateTime($start) );
                $reservation->setEnd(new \DateTime($end) );
  
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($reservation);
                $entityManager->flush();            
            }
           
        }
           
        $response = new Response(json_encode($stop));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
        // return $this->render('reserver/index.html.twig', //['var' => $var, 'user' => $user, 'reservataire' => $reservataire ]
            //['var' => $var,
            //'form' => $form->createView(),
              // ]
           // );
    }
    
    /**
    * @Route("/delete", name="reservation_delete", methods={"GET","POST"})
    */
    public function delete(Request $request): Response
    {
        // attributs de session
        $user = $this->container->get('security.token_storage')->getToken()->getUser()->getId();
            if($request->isXmlHttpRequest()) {
                $id = $request->get('id');
                $repository = $this->getDoctrine()->getRepository(Reservation::class);
                $reservation = $repository->find($id);                 
                $reservataire = $reservation->getReservataire($id);

            // Seul le réservataire ( le User d'origine) peut supprimer sa réservation 
               if ($user !== $reservataire)   
                {
                    $stop = true;
                }else{
                    $stop = false; 
                  
            //if ($this->isCsrfTokenValid('delete'.$reserver->getId($id), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($reservation);
                $entityManager->flush();
                }
        }
        $response = new Response(json_encode($stop));//($stop));
        $response->headers->set('Content-Type', 'application/json');
        return $response;        
        //return $this->render('reserver/index.html.twig');
        //return $this->redirectToRoute('reserver_index');
    }

    //<------------------------------------------------------------->
    //<-- partie hors requête Ajax de index.html.twig -->
    //<-- Utilisé pour le formulaire classique de réservation -->
        
    /**
     * @Route("/ajout", name="reservation_ajout", methods={"GET","POST"})
     */
    public function ajout(Request $request): Response
    {
 
		// attributs de session
        $session = $this->get("session");
        $auteur = $this->getUser('session')->getId();
        $user = $this->getUser('session')->getUsername();

        // On instancie une nouvelle reservation
        $reservation = new Reservation();
                        // - 4 - on attribue code Reservation
        $CodeReservation = rand(0,10000);
        //echo"code".$CodeReservation;
        //exit;
        
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        $session = $this->get("session");
        $auteur = $this->getUser('session')->getId();
        $user = $this->getUser('session')->getUsername();
		$instructeur = $reservation->getInstructeur();
		$resourceId = $reservation->getResourceId();
        
        $em = $this->getDoctrine()->getManager(); 

			if ($instructeur == true){
				$initiale = $reservation->getInstructeur()->getInitiales();
				$title	= $user." / ".$initiale;
				$reservation->setTitle($title);
                $reservation->setFormateur($reservation->getInstructeur() );
			}else{			
			$reservation->setTitle($user);
            $reservation->setFormateur("Néant");
			}

        $reservation->setCodeReservation($CodeReservation);			
		$reservation->setReservataire($auteur);
		$reservation->SetResourceId($reservation->getAvion()->getAvion()->getId());
		$reservation->setInstructeur($reservation->getInstructeur() );
            $title = $reservation->getAvion()->getAvion()->getTitle();	
            $immat = $reservation->getAvion()->getAvion()->getType();
            $identif = $immat."-".$title;        
        $reservation->SetAppareil($identif);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($reservation);
        $entityManager->flush();

            return $this->redirectToRoute('reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reserver/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);

    }

}

