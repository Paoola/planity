<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\User;
use App\Entity\Saloon;
use App\Entity\Vacation;
use App\Form\VacationType;

class VacationController extends AbstractController
{
    /**
     * @Route("/saloon/manage/vacation/edit/{saloon}", name="saloon_manage_vacation_edit")
     */
    public function editSaloon(Request $request, Saloon $saloon)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $vacation = $entityManager->getRepository(Vacation::class)->findOneBySaloon($saloon);
        if (null === $vacation) {
          $vacation = new Vacation();
          $vacation->setSaloon($saloon);
        }
        
        $form = $this->createForm(VacationType::class, $vacation);
        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isValid()) {
            $entityManager->persist($vacation);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success','Le calendrier a bien été modifié');

            return $this->redirectToRoute('saloon_manage_vacation_edit', array('saloon' => $saloon->getId()));
        }

        return $this->render('saloon/vacation/saloon.html.twig', array(
        	'saloon' => $saloon,
            'form' => $form->createView(),
            'select' => 'fermeture'
        ));
    }

    /**
     * @Route("/saloon/manage/vacation/edit/{saloon}/{worker}", name="saloon_manage_vacation_worker_edit")
     */
    public function editWorker(Request $request, Saloon $saloon, User $worker)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $vacation = $entityManager->getRepository(Vacation::class)->findOneByWorker($worker);
        if (null === $vacation) {
          $vacation = new Vacation();
          $vacation->setWorker($worker);
        }
        
        $form = $this->createForm(VacationType::class, $vacation);
        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isValid()) {
            $entityManager->persist($vacation);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success','Le calendrier a bien été modifié');

            return $this->redirectToRoute('saloon_manage_worker', array('saloon' => $saloon->getId()));
        }

        return $this->render('saloon/vacation/worker.html.twig', array(
        	'saloon' => $saloon,
            'form' => $form->createView(),
            'select' => 'congés'
        ));
    }
}
