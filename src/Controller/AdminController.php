<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Saloon;
use App\Entity\Schedule;
use App\Form\SaloonAdminType;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin-saloon", name="admin")
     */
    public function index(Request $request)
    {
    	$entityManager = $this->getDoctrine()->getManager();
    	$saloon = new Saloon();

        // Ajout des horaires
        $start = '09:00';
        $end   = '19:00';
        for ($i = 0; $i != 7; $i++) {
            $schedule = new Schedule();
            $schedule->setDay($i);
            $schedule->setStart(\DateTime::createFromFormat('H:i', $start));
            $schedule->setEnd(\DateTime::createFromFormat('H:i', $end));
            $saloon->addSchedule($schedule);
        }

    	$form = $this->createForm(SaloonAdminType::class, $saloon);

        $form->handleRequest($request);
    	if ($form->isSubmitted() && $form->isValid()) {

            $saloon->setBookingActive(false);

    		$entityManager->persist($saloon);
            $entityManager->flush();
            
            return $this->render('admin/confirmation.html.twig');
    	}

        return $this->render('admin/index.html.twig', array(
        	'form' => $form->createView(),
        ));
    }
}
