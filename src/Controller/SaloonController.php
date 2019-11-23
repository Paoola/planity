<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\Schedule;
use App\Entity\Saloon;
use App\Entity\Slot;
use App\Form\SaloonType;
use App\Form\SubscribeType;

class SaloonController extends AbstractController
{
    /**
     * @Route("/saloon/manage", name="saloon_choice")
     */
    public function choice()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $saloons = $entityManager->getRepository(Saloon::class)->findSaloonsByManager($user);

        if (empty($saloons)) {
        	return $this->redirectToRoute('saloon_add');
        } elseif (count($saloons) == 1) {
        	return $this->redirectToRoute('saloon_manage', array('saloon' => $saloons[0]->getId()));
        }

        return $this->render('saloon/choice.html.twig', array(
        	'saloons' => $saloons
        ));
    }

    /**
	 * Ajouter un salon
     *
     * @Route("/saloon/add", name="saloon_add")
     */
    public function add(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $saloon = new Saloon();
        $form = $this->createForm(SaloonType::class, $saloon);

        $form->handleRequest($request);
	    if ($form->isSubmitted() && $form->isValid()) {

	    	$saloon->addManager($this->getUser());
            $saloon->addWorker($this->getUser());

            $user = $this->getUser();
            $user->addRole('ROLE_MANAGER');
            $user->addRole('ROLE_WORKER');

            // Ajout des horaires
            $start = '09:00';
            $end   = '19:00';

            for ($i = 0; $i != 7; $i++) {
                $schedule = new Schedule();
                $schedule->setDay($i);
                $schedule->setSaloon($saloon);
                $schedule->setStart(\DateTime::createFromFormat('H:i', $start));
                $schedule->setEnd(\DateTime::createFromFormat('H:i', $end));
                $entityManager->persist($schedule);
            }

            for ($i = 0; $i != 7; $i++) {
                $schedule = new Schedule();
                $schedule->setDay($i);
                $schedule->setWorker($this->getUser());
                $schedule->setStart(\DateTime::createFromFormat('H:i', $start));
                $schedule->setEnd(\DateTime::createFromFormat('H:i', $end));
                $entityManager->persist($schedule);
            }
            
            $entityManager->persist($user);
	        $entityManager->persist($saloon);
	        $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success','Votre salon a bien été ajouté');

	        return $this->redirectToRoute('saloon_subscribe', array('saloon' => $saloon->getId()));
	    }

        return $this->render('saloon/add.html.twig', array(
        	'form' => $form->createView(),
        ));
    }

    /**
     * Modifier un salon
     *
     * @Route("/saloon/edit/{saloon}", name="saloon_edit")
     */
    public function edit(Request $request, Saloon $saloon)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(SaloonType::class, $saloon, array('labelButton' => 'Modifier'));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($saloon);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success','Votre salon a bien été modifié');

            return $this->redirectToRoute('saloon_manage', array('saloon' => $saloon->getId()));
        }

        $formUnsubscribe = $this->createFormBuilder()
            ->add('submit', SubmitType::class, array(
                'label' => 'Arrêter mon abonnement',
                'attr' => array('class' => 'mt-5 btn btn-secondary btn-block')
            ))
            ->getForm();
        $formUnsubscribe->handleRequest($request);
        if ($saloon->getIsPremium() && $formUnsubscribe->isSubmitted() && $formUnsubscribe->isValid()) {
            \Stripe\Stripe::setApiKey(getenv('SK_STRIPE'));
            $subscription = \Stripe\Subscription::retrieve($saloon->getSubscriptionStripeId());
            $subscription->cancel();

            $saloon->setIsPremium(false);
            $entityManager->persist($saloon);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success','Votre abonnement est maintenant arrêté');
        }

        return $this->render('saloon/edit.html.twig', array(
            'form' => $form->createView(),
            'saloon' => $saloon,
            'formUnsubscribe' => $formUnsubscribe->createView(),
        ));
    }

    /**
	 * Manager un salon
     *
     * @Route("/saloon/manage/{saloon}", name="saloon_manage")
     */
    public function manage(Saloon $saloon)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $todaySlots = $entityManager->getRepository(Slot::class)->findBySaloonAndDate($saloon->getId(), new \Datetime('now'));
        $nextSlots = $entityManager->getRepository(Slot::class)->findByNextSaloon($saloon->getId(), new \Datetime('now'));

        return $this->render('saloon/manage.html.twig', array(
        	'saloon' => $saloon,
            'todaySlots' => $todaySlots,
            'nextSlots' => $nextSlots,
        ));
    }

    /**
     * Abonner un salon
     *
     * @Route("/saloon/subscribe/{saloon}", name="saloon_subscribe")
     */
    public function subscribe(Saloon $saloon, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $form = $this->createForm(SubscribeType::class, $saloon);
        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isValid()) {

            $promoCode = ['jura', 'besancon', 'besançon', 'lons'];
            $freeMonthCode = ['B2OC33', 'boocut-start'];
            if (in_array(strtolower($form->getData()->getPromo()), $promoCode)) {
                $coupon = getenv('medium_coupon_stripe');
            } elseif (in_array(strtolower($form->getData()->getPromo()), $freeMonthCode)) {
                $coupon = 'one-month';
            } else {
                $coupon = null;
            }

            \Stripe\Stripe::setApiKey(getenv('SK_STRIPE'));

            $customer = \Stripe\Customer::create(array(
                'email' => $user->getEmail(),
                'source' => $request->request->get('stripeToken'),
            ));

            $subscription = \Stripe\Subscription::create([
                'customer' => $customer->id,
                'items' => [['plan' => getenv('plan_id_stripe')]],
                'trial_from_plan' => true,
                'coupon' => $coupon,
            ]);

            if ($subscription->status == 'active' || $subscription->status == 'trialing') {
                $saloon->setStripeId($customer->id);
                $saloon->setSubscriptionStripeId($subscription->id);
                $saloon->setIsPremium(true);

                $em->persist($saloon);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success','Votre salon est maintenant premium !');

                return $this->redirectToRoute('saloon_manage', array('saloon' => $saloon->getId()));
            }
        }

        return $this->render('saloon/subscribe.html.twig', array(
            'saloon' => $saloon,
            'form' => $form->createView(),
        ));
    }
}
