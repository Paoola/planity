<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Saloon;
use App\Entity\Schedule;
use App\Form\ScheduleType;

class ScheduleController extends AbstractController
{
    /**
     * @Route("/saloon/manage/schedule/{saloon}", name="saloon_manage_schedule")
     */
    public function index(Saloon $saloon)
    {
        return $this->render('saloon/schedule/index.html.twig', array(
        	'saloon' => $saloon,
        ));
    }

    /**
     * @Route("/saloon/manage/schedule/edit/{saloon}/{schedule}", name="saloon_manage_schedule_edit")
     */
    public function edit(Saloon $saloon, Schedule $schedule, Request $request)
    {
    	$entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(ScheduleType::class, $schedule);
        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isValid()) {

        	$entityManager->persist($schedule);
        	$entityManager->flush();

            $this->get('session')->getFlashBag()->add('success','L\'horaire a bien été modifié');

            // if($worker) {
            //     return $this->redirectToRoute('admin_worker', array('saloon' => $saloon->getId(), 'worker' => $worker->getId()));
            // } else {
                return $this->redirectToRoute('saloon_manage_schedule', array('saloon' => $saloon->getId()));
            // }
        }

        return $this->render('saloon/schedule/edit.html.twig', array(
        	'saloon' => $saloon,
        	'schedule' => $schedule,
        	'form' => $form->createView(),
        ));
    }
}
