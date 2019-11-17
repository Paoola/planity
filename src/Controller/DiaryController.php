<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Saloon;
use App\Entity\Slot;
use App\Form\SlotType;

class DiaryController extends AbstractController
{
    /**
     * @Route("/saloon/diary", name="saloon_diary")
     */
    public function index()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $slots = $entityManager->getRepository(Slot::class)->findByNextWorker(
            $this->getUser()->getId(),
            new \Datetime('now')
        );

        return $this->render('diary/index.html.twig', array(
            'slots' => $slots
        ));
    }

    /**
     * @Route("/saloon/diary/new", name="saloon_diary_new")
     */
    public function new(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $saloon = $this->getUser()->getSaloons()[0];
        $slot = new Slot();
        $slot->setSaloon($saloon);
        $slot->setWorker($this->getUser());
        $form = $this->createForm(SlotType::class, $slot, array('labelButton' => 'Ajouter'));
        $form->remove('worker');

        $form->handleRequest($request);
        if ($request->isMethod('POST') && $form->isValid()) {

            $slot->setIsValid(true);

            $entityManager->persist($slot);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success','Le rendez-vous a bien été ajouté');

            return $this->redirectToRoute('saloon_diary');
        }

        return $this->render('diary/new.html.twig', array(
            'saloon' => $saloon,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/saloon/diary/edit/{slot}", name="saloon_diary_edit")
     */
    public function edit(Slot $slot, Request $request)
    {
    	$entityManager = $this->getDoctrine()->getManager();
        $saloon = $this->getUser()->getSaloons()[0];
        $form = $this->createForm(SlotType::class, $slot);
        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isValid()) {

        	$entityManager->persist($slot);
        	$entityManager->flush();

            $this->get('session')->getFlashBag()->add('success','Le rendez-vous a bien été modifié');

            return $this->redirectToRoute('saloon_diary');
        }

        return $this->render('diary/edit.html.twig', array(
        	'saloon' => $saloon,
        	'slot' => $slot,
        	'form' => $form->createView(),
        ));
    }
}
