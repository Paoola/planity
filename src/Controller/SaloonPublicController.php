<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Saloon;

class SaloonPublicController extends AbstractController
{
    /**
     * @Route("/coiffeur/{saloon}", name="saloon_public")
     */
    public function index($saloon)
    {
    	$entityManager = $this->getDoctrine()->getManager();
    	$saloon = $entityManager->getRepository(Saloon::class)->findOneBySlug($saloon);

        return $this->render('saloonPublic/index.html.twig', array(
        	'saloon' => $saloon,
        ));
    }
}
