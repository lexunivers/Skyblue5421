<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormView;
use Doctrine\Common\Persistence\ObjectManager;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use App\Entity\Avions;
use App\Entity\Vol;
use App\Entity\SonataUserUser;
use App\Entity\OperationComptable;

class VolController extends Controller
{
    #[Route('/vol', name: 'app_vol')]
    public function index(): Response
    {
        return $this->render('vol/index.html.twig', [
            'controller_name' => 'VolController',
        ]);
    }    
}
