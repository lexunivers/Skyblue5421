<?php

namespace App\Controller;

use App\Entity\CotisationClub;
use App\Repository\CotisationClubRepository;
use App\Entity\MaCotisation;
use App\Form\MaCotisationType;
use App\Repository\MaCotisationRepository;
use App\Entity\OperationComptable;
use App\form\OperationComptableType;
use App\form\OperationComptableEditType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/ma/cotisation")
 */
class MaCotisationController extends AbstractController
{
    /**
     * @Route("/", name="ma_cotisation_index", methods={"GET"})
     */
    public function index(Request $request, CotisationClubRepository $CotisationClubRepository): Response
    {

		return $this->render('ma_cotisation/index.html.twig', [	//'editMode'=>$operation->getId()!== null,
            'ma_cotisations' => $CotisationClubRepository->findBy(['validation' => true], ['id' => 'ASC'],  2,
					0) //myfindAnnee($id)
                ]);
    }

    /**
     * @Route("/new", name="ma_cotisation_new", methods={"GET","POST"})
     */
    public function new(Request $request ): Response
    {

        $maCotisation = new MaCotisation();

		$maCotisation->setUser($this->container->get('security.token_storage')->getToken()->getUser());

        $user = $this->getUser()->getId();

		// attributs de session
        $session = $this->get("session");
        $auteur = $this->getUser('session')->getId();
        $annuel = $session->get('annee');
		
        $em = $this->getDoctrine()->getManager();

		$annee = $em->getRepository('App\Entity\CotisationClub')->myfindAnnee();
          
		$listeCotisation = $em->getRepository('App\Entity\MaCotisation')->findBy(array('user' => $user));

        $annee = $session->get('annee');

            foreach($listeCotisation as $cotisation)
            {        
				if ('userId_exists' == true && 'annuel_exists'== true){

				return $this->redirectToRoute('ma_cotisation_Afficher',['id' => $cotisation->getId()]);
					}				
            }


        $form = $this->createForm(MaCotisationType::class, $maCotisation);
	
        $operation = new OperationComptable();
        $operation->setCompteId($this->container->get('security.token_storage')->getToken()->getUser()->getId());

        // On lie MaCotisation à OperationComptable  
        $maCotisation->setComptable($operation);

        if (null === $maCotisation) {
            throw new NotFoundHttpException("La cotisation d'id ".$id." n'existe pas.");
        }
       
																			
        if ($request->isMethod('POST') && $form->handleRequest($request) && $form->isSubmitted() && $form->isValid()){

            $maCotisation->setAnnee($maCotisation->getAnnee() );
            $maCotisation->setCotisation($maCotisation->getCotisation() );
            $maCotisation->setInfoPilote($maCotisation->TarifInfoPilote() );			
            $maCotisation->setLicenceFFA($maCotisation->getLicenceFFA() );

            $operation->setUser($maCotisation->getUser());
            $operation->setOperMontant($maCotisation->getTotalCotisation() );
            $operation ->setOperSensMt(0);
            $operation->setLibelle($maCotisation->getLibelleCotis()); 

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($maCotisation);
            $entityManager->persist($operation);            
            $entityManager->flush();

            $request->getSession()->getFlashBag()->add('Info', 'le Montant de votre Cotisation est inscrit dans votre Cpte Pilote.');

            return $this->redirectToRoute('ma_cotisation_show',['id' => $maCotisation->getId() ,
                                                                'OperationComptable'=>$operation->getId(),
                                                                 ] );
		}

            return $this->render('ma_cotisation/new.html.twig', [
                'ma_cotisation' => $maCotisation,
                'operationComptable'=>$operation->getId(),
                'validation' => true,
                //'editMode'=>$operation->getId() !== null,
                //'editMode'=>$maCotisation,
                'form' => $form->createView(),
            ]);
    }

    /**
     * @Route("/Afficher/{id}", name="ma_cotisation_Afficher", methods={"GET"})
     */
    public function Afficher(MaCotisation $maCotisation): Response
    {
        return $this->render('ma_cotisation/Afficher.html.twig', [
            'ma_cotisation' => $maCotisation,
        ]);
    }

    /**
     * @Route("/{id}", name="ma_cotisation_show", methods={"GET"})
     */
    public function show(MaCotisation $maCotisation): Response
    {
        return $this->render('ma_cotisation/show.html.twig', [
            'ma_cotisation' => $maCotisation,
			'editMode' =>$maCotisation,
        ]);
    }



    /**
    * @Route("/{id}/edit", name="ma_cotisation_edit", methods={"GET","POST"})
    */
    public function edit(Request $request, MaCotisation $maCotisation, $id ): Response
    {

        $em = $this->getDoctrine()->getManager();
        $operation = $em->getRepository('App\Entity\OperationComptable')->find($id);

        $maCotisation->setUser($this->container->get('security.token_storage')->getToken()->getUser());

        $form = $this->createForm(MaCotisationType::class, $maCotisation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $maCotisation->setAnnee($maCotisation->getAnnee() );
            $maCotisation->setCotisation($maCotisation->getCotisation() );
            $maCotisation->setInfoPilote($maCotisation->TarifInfoPilote() );			
            $maCotisation->setLicenceFFA($maCotisation->getLicenceFFA() );
            $maCotisation->getComptable($operation)->setOperMontant($maCotisation->getTotalCotisation() );
           
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ma_cotisation_show',['id' => $maCotisation->getId() ,
																//'editMode'=>$operation->getId() ,
                                                                 ] );
        }

        return $this->render('ma_cotisation/edit.html.twig', [
            'ma_cotisation' => $maCotisation,
			//'editMode'=>$operation->getId() !== null,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ma_cotisation_delete", methods={"POST"})
     */
    public function delete(Request $request, MaCotisation $maCotisation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$maCotisation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($maCotisation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ma_cotisation_index');
    }
}
