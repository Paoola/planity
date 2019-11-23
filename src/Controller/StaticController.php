<?php

namespace App\Controller;

use App\Entity\Price;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class StaticController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $prices = $entityManager->getRepository(Price::class)->findAll();

        return $this->render('static/homepage.html.twig', array(
            'prices' => $prices,
        ));
    }
}
