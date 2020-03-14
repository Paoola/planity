<?php

namespace App\Controller;

use App\Entity\Slot;
use App\Form\SlotType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Saloon;

class StaticController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $saloons = $entityManager->getRepository(Saloon::class)->findAll();
        shuffle($saloons);

        $saloons = array_slice($saloons, 0, 3);

        return $this->render('static/homepage.html.twig', array(
            'saloons' => $saloons
        ));
    }

}
