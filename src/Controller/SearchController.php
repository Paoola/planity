<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Saloon;

class SearchController extends AbstractController
{
    /**
     * @Route("/recherche", name="search")
     */
    public function index(Request $request)
    {
        $latitude = $request->request->get('latitude');
        $longitude = $request->request->get('longitude');

    	$entityManager = $this->getDoctrine()->getManager();
    	$saloons = $entityManager->getRepository(Saloon::class)->findClosest($latitude, $longitude);

        return $this->render('search/index.html.twig', array(
        	'saloons' => $saloons,
        ));
    }
}
