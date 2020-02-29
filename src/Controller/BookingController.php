<?php

namespace App\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use App\Entity\User;
use App\Entity\Price;
use App\Entity\Slot;
use App\Entity\Customer;
use App\Entity\Vacation;
use App\Entity\Saloon;
use App\Form\CustomerType;
use App\Form\SlotConfirmationType;
use App\Service\Mailer;
use App\Service\Sms;
use App\Service\ManageSlot;

class BookingController extends AbstractController
{
    /**
     * @Route("coiffeur/reservation/success/{saloon}", name="saloon_public_booking_success")
     */
    public function success($saloon)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $saloon = $entityManager->getRepository(Saloon::class)->findOneBySlug($saloon);

        return $this->render('booking/success.html.twig', array(
            'saloon' => $saloon
        ));
    }

    /**
     * @Route("coiffeur/reservation/{timestamp}/{price}", name="saloon_public_booking")
     */
    public function index($timestamp = 0, Price $price)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $prices = $entityManager->getRepository(Price::class)->findAll();
        $date = new \DateTime();

        return $this->render('booking/index.html.twig', array(
            'price'     => $price,
            'prices'     => $prices,
            'date' => $date
        ));
    }

    /**
     * @Route("coiffeur/reservation/{slot_ts}/{price}/{worker}", name="saloon_public_booking_form")
     */
    public function booking(Request $request, $slot_ts, Price $price, User $worker, Mailer $mailer, Sms $sms)
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

            // Paiement
            if (!empty($request->request->get('stripeToken'))) {

                \Stripe\Stripe::setApiKey(getenv('SK_STRIPE'));
                $charge = \Stripe\Charge::create([
                    'amount' => $price->getAmount() * 100,
                    'currency' => 'eur',
                    'description' => 'Réservation - ' . $saloon->getName(),
                    'source' => $request->request->get('stripeToken'),
                    'statement_descriptor' => 'Réservation boocut.fr',
                ]);

                if ($charge->status == 'succeeded') {
                    $slot->setIsPaid(true);
                    $slot->setIsValid(true);
                    $saloon->setMoney($saloon->getMoney() + $price->getAmount() * 100);

                    $entityManager->persist($slot);
                    $entityManager->persist($saloon);
                    $entityManager->flush();

                    $text = 'Vous avez une réservation au salon '. $saloon->getName() .' le ' . $slot->getStart()->format('d/m') . ' à ' . $slot->getStart()->format('H:i');
                    $sms->send($text, $customer->getPhoneNumber());
                    $mailer->send('Boocut - Confirmation de rendez-vous', $customer->getEmail(), 'confirmationBooking', array('slot' => $slot));

                    $session = new Session();
                    $session->set('bookingStart', $start);
                    $session->set('bookingEnd', $end);

                    return $this->redirectToRoute('saloon_public_booking_success', array('saloon' => $saloon->getSlug()));
                }
            } else {
                $slot->setConfirmationCode(rand(1000, 9999));
                $slot->setIsPaid(false);
                $slot->setIsValid(false);

                $text = 'Votre code de confirmation de rendez-vous est : ' . $slot->getConfirmationCode();
                $sms->send($text, $customer->getPhoneNumber());

                $entityManager->persist($slot);
                $entityManager->flush();

                return $this->redirectToRoute('saloon_public_booking_sms', array(
                    'slot' => $slot->getId(),
                ));
            }

            return $this->redirectToRoute('homepage');
        }

        return $this->render('booking/book.html.twig', array(
            'saloon' => $saloon,
            'price' => $price,
            'slot' => $slot,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("coiffeur/reservation-sms/{slot}", name="saloon_public_booking_sms")
     */
    public function smsConfirmationAction(Request $request, Slot $slot, Sms $sms)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(SlotConfirmationType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('confirmationCode')->getData() == $slot->getConfirmationCode()) {
                $slot->setIsValid(true);
                $em->persist($slot);
                $em->flush();

                $text = 'Vous avez une réservation au salon ' . $slot->getSaloon()->getName() . ' le ' . $slot->getStart()->format('d/m') . ' à ' . $slot->getStart()->format('H:i');
                $sms->send($text, $slot->getCustomer()->getPhoneNumber());

                $session = new Session();
                $session->set('bookingStart', $slot->getStart());
                $session->set('bookingEnd', $slot->getEnd());

                return $this->redirectToRoute('saloon_public_booking_success', array('saloon' => $slot->getSaloon()->getSlug()));
            } else {
                $form->get('confirmationCode')->addError(
                    new FormError('Mauvais code de confirmation')
                );
            }
        }

        return $this->render('booking/confirmation.html.twig', array(
            'form' => $form->createView(),
            'saloon' => $slot->getSaloon(),
            'slot' => $slot,
        ));
    }

    public function getSlots(Price $price, User $worker, $timestamp, ManageSlot $slot, $auto, $test = 0)
    {
        $error = null;

        //$day = new \DateTime($timestamp);
        $day = \DateTime::createFromFormat('U', $timestamp);

        $em = $this->getDoctrine()->getManager();
        $vacationSaloon = $em->getRepository(Vacation::class)->findOneBySaloon($price->getSaloon());
        $vacationWorker = $em->getRepository(Vacation::class)->findOneByWorker($worker);
        if (null !== $vacationSaloon) {
            foreach ($vacationSaloon->getDays() as $vacationDay) {
                $vacationDay = substr($vacationDay, 1);
                $vacationDay = substr($vacationDay, 0, -1);
                if ($day->format('d/m/Y') == $vacationDay) {
                    $error = 'Le salon sera fermé ce jour-ci';
                }
            }
        }

        if (null !== $vacationWorker) {
            foreach ($vacationWorker->getDays() as $vacationDay) {
                $vacationDay = substr($vacationDay, 1);
                $vacationDay = substr($vacationDay, 0, -1);
                if ($day->format('d/m/Y') == $vacationDay) {
                    $error = 'Cette personne ne sera pas disponible ce jour-ci';
                }
            }
        }
        // Récupération des slots disponibles
        $slots = $slot->freeTime($worker, $day, $price->getDuration() * 60);

        return $this->render('booking/slots.html.twig', array(
            'slots' => $slots,
            'price' => $price,
            'worker' => $worker,
            'day' => $day,
            'error' => $error,
            'auto' => $auto
        ));
    }
}
