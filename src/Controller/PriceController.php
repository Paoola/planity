<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Saloon;
use App\Entity\Price;
use App\Entity\User;
use App\Form\PriceType;

class PriceController extends AbstractController
{
    /**
     * @Route("/saloon/manage/price/{saloon}", name="saloon_manage_price")
     */
    public function index(Saloon $saloon)
    {
        return $this->render('saloon/price/index.html.twig', array(
        	'saloon' => $saloon,
        ));
    }

    /**
     * @Route("/saloon/manage/price/new/{saloon}", name="saloon_manage_price_new")
     */
    public function new(Saloon $saloon, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $price = new Price();
        $price->setSaloon($saloon);
        $form = $this->createForm(PriceType::class, $price, array('labelButton' => 'Ajouter'));
        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isValid()) {

            $entityManager->persist($price);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success','Le prix a bien été ajouté');

            return $this->redirectToRoute('saloon_manage_price', array('saloon' => $saloon->getId()));
        }

        return $this->render('saloon/price/new.html.twig', array(
            'saloon' => $saloon,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/saloon/manage/price/edit/{saloon}/{price}", name="saloon_manage_price_edit")
     */
    public function edit(Saloon $saloon, Price $price, Request $request)
    {
    	$entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(PriceType::class, $price);
        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isValid()) {

        	$entityManager->persist($price);
        	$entityManager->flush();

            $this->get('session')->getFlashBag()->add('success','Le prix a bien été modifié');

            return $this->redirectToRoute('saloon_manage_price', array('saloon' => $saloon->getId()));
        }

        return $this->render('saloon/price/edit.html.twig', array(
        	'saloon' => $saloon,
        	'price' => $price,
        	'form' => $form->createView(),
        ));
    }
}
