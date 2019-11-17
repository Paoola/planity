<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Slot;
use App\Entity\Saloon;

class CustomerController extends AbstractController
{
    /**
     * @Route("/saloon/manage/customer/{saloon}", name="saloon_manage_customer")
     */
    public function index(Saloon $saloon)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $slots = $entityManager->getRepository(Slot::class)->findBy(array('saloon' => $saloon));

        return $this->render('customer/index.html.twig', array(
            'slots' => $slots,
            'saloon' => $saloon
        ));
    }
}
