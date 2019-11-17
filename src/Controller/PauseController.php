<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Form\PauseType;
use App\Entity\Saloon;
use App\Entity\Pause;

class PauseController extends AbstractController
{
    /**
     * @Route("/saloon/manage/pause/{saloon}", name="saloon_manage_pause")
     */
    public function index(Saloon $saloon)
    {
        return $this->render('saloon/pause/index.html.twig', array(
        	'saloon' => $saloon,
        ));
    }

    /**
     * @Route("/saloon/manage/pause/new/{saloon}", name="saloon_manage_pause_new")
     */
    public function new(Saloon $saloon, Request $request)
    {
    	$entityManager = $this->getDoctrine()->getManager();
        $pause = new Pause();
        $pause->setSaloon($saloon);
        $form = $this->createForm(PauseType::class, $pause, array('labelButton' => 'Ajouter'));
        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isValid()) {

            $entityManager->persist($pause);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success','La pause a bien été ajouté');

            return $this->redirectToRoute('saloon_manage_pause', array('saloon' => $saloon->getId()));
        }

        return $this->render('saloon/pause/new.html.twig', array(
        	'saloon' => $saloon,
        	'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/saloon/manage/pause/edit/{saloon}/{pause}", name="saloon_manage_pause_edit")
     */
    public function edit(Request $request, Saloon $saloon, Pause $pause)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(PauseType::class, $pause, array('labelButton' => 'Modifier'));
        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isValid()) {

            $entityManager->persist($pause);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success','La pause a bien été modifié');

            return $this->redirectToRoute('saloon_manage_pause', array('saloon' => $saloon->getId()));
        }

        return $this->render('saloon/pause/edit.html.twig', array(
        	'saloon' => $saloon,
        	'form' => $form->createView(),
        ));
    }
}
