<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormView;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\Common\Persistence\ObjectManager;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Bundle\PaginatorBundle\Definition;
use App\Entity\SonataUserUser;
use App\Entity\OperationComptable;
use App\Form\OperationComptableType;
use App\Form\OperationComptableEditType;
use App\Repository\OperationComptableRepository;
use Sonata\AdminBundle\Route\RouteCollection;

class OperationComptableController extends Controller
{
    /**
     * @Route("/operation_comptable", name="operationcomptable")
     */
    public function index()
    {
        return $this->render('operation_comptable/index.html.twig', [
            'controller_name' => 'OperationComptableController',
        ]);
    }

    public function AjouterEcritureAction(OperationComptable $Operation = null, Request $request, ObjectManager $manager = null)
    {
        $Operation = new OperationComptable();
                
        $form    = $this->createForm(OperationComptableType::class, $Operation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($Operation);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Votre Compte a bien été mis à jour.');

            // $this->addFlash('info', 'Profile updated');
            // $this->addFlash('warning', 'Profile issued');

            return $this->redirect($this->generateUrl('sky_compte_ajouter', array('id' => $Operation->getId())));
        }
                    
        return $this->render('/MonCompte/AjoutEcritures.html.twig', array(
            'formMonCompte'    => $form->createView(),
            'editMode' => $Operation->getId() !== null                
        ));
    }
}
