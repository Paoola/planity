<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Saloon;
use App\Entity\Slot;
use App\Entity\Price;
use App\Entity\User;
use App\Entity\Booking;
use App\Entity\Customer;
use App\Service\Mailer;
use App\Service\Sms;
use App\Form\SlotType;
use App\Form\CustomerType;

class SlotController extends AbstractController
{
    /**
     * @Route("/saloon/manage/slot/{saloon}", name="saloon_manage_slot")
     */
    public function index(Saloon $saloon)
    {
        $entityManager = $this->getDoctrine()->getManager();
        //ici récupérer toutes les réservations pour chaque price
        $nextSlots = $entityManager->getRepository(Slot::class)->findByNextSaloon($saloon->getId(), new \Datetime('now'));

        return $this->render('saloon/slot/index.html.twig', array(
        	'saloon' => $saloon,
            'nextSlots' => $nextSlots
        ));
    }

    /**
     * @Route("/saloon/manage/slot/new/{saloon}", name="saloon_manage_slot_new")
     */
    public function new(Saloon $saloon, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $slot = new Slot();
        $slot->setSaloon($saloon);
        $form = $this->createForm(SlotType::class, $slot, array('labelButton' => 'Ajouter'));
        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isValid()) {

            $slot->setIsValid(true);

            $entityManager->persist($slot);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success','Le rendez-vous a bien été ajouté');

            return $this->redirectToRoute('saloon_manage_slot', array('saloon' => $saloon->getId()));
        }

        return $this->render('saloon/slot/new.html.twig', array(
            'saloon' => $saloon,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/saloon/manage/slot/new-auto/{saloon}", name="saloon_manage_slot_new_auto")
     */
    public function newAuto(Saloon $saloon, Request $request)
    {
        return $this->render('saloon/slot/newAuto.html.twig', array(
            'saloon' => $saloon,
        ));
    }

    /**
     * @Route("saloon/manage/slot/new-auto/{timestamp}/{price}", name="saloon_private_booking")
     */
    public function bookingAuto($timestamp = 0, Price $price)
    {
        return $this->render('saloon/slot/bookingAuto.html.twig', array(
            'price'     => $price,
            'timestamp' => $timestamp
        ));
    }

    /**
     * @Route("saloon/manage/slot/new-auto/{slot_ts}/{price}/{worker}", name="saloon_private_booking_form")
     */
    public function bookingForm(Request $request, $slot_ts, Price $price, User $worker, Mailer $mailer, Sms $sms)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $slot = new Slot();
        $customer = new Customer();
        $saloon = $price->getSaloon();
        $form = $this->createForm(CustomerType::class, $customer);
        $start = new \DateTime();
        $end = new \DateTime();
        $start->setTimestamp($slot_ts);
        $end->setTimestamp($slot_ts + $price->getDuration() * 60);
        $slot->setStart($start);
        $slot->setEnd($end);
        $slot->setWorker($worker);
        $slot->setSaloon($price->getSaloon());
        $slot->setPrice($price);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $slot->setCustomer($customer);
            $entityManager->persist($slot);

            $slot->setIsPaid(false);
            $slot->setIsValid(true);

            $entityManager->persist($slot);
            $entityManager->persist($saloon);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success','Le rendez-vous a bien été ajouté');

            return $this->redirectToRoute('saloon_manage_slot', array('saloon' => $saloon->getId()));
        }

        return $this->render('saloon/slot/form.html.twig', array(
            'saloon' => $saloon,
            'price' => $price,
            'slot' => $slot,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/saloon/manage/slot/edit/{saloon}/{slot}", name="saloon_manage_slot_edit")
     */
    public function edit(Saloon $saloon, Slot $slot, Request $request)
    {
    	$entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(SlotType::class, $slot);
        $form->handleRequest($request);
        if ($request->isMethod('POST') && $form->isValid()) {

        	$entityManager->persist($slot);
        	$entityManager->flush();

            $this->get('session')->getFlashBag()->add('success','Le rendez-vous a bien été modifié');

            return $this->redirectToRoute('saloon_manage_slot', array('saloon' => $saloon->getId()));
        }

        return $this->render('saloon/slot/edit.html.twig', array(
        	'saloon' => $saloon,
        	'slot' => $slot,
        	'form' => $form->createView(),
        ));
    }
}
