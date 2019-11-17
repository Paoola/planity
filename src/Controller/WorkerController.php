<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Form\RegisterType;
use App\Entity\Saloon;
use App\Entity\User;
use App\Entity\Schedule;

class WorkerController extends AbstractController
{
    /**
     * @Route("/saloon/manage/worker/{saloon}", name="saloon_manage_worker")
     */
    public function index(Saloon $saloon)
    {
        return $this->render('saloon/worker/index.html.twig', array(
        	'saloon' => $saloon,
        ));
    }

    /**
     * @Route("/saloon/manage/worker/new/{saloon}", name="saloon_manage_worker_new")
     */
    public function new(Saloon $saloon, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
    	$entityManager = $this->getDoctrine()->getManager();
        $worker = new User();
        $worker->addSaloonsWorker($saloon);
        $worker->addRole('ROLE_WORKER');
        $form = $this->createForm(RegisterType::class, $worker, array('labelButton' => 'Ajouter'));
        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isValid()) {

        	$password = $passwordEncoder->encodePassword($worker, $worker->getPlainPassword());
            $worker->setPassword($password);

            // Ajout des horaires
            $start = '09:00';
            $end   = '19:00';

            for ($i = 0; $i != 7; $i++) {
                $schedule = new Schedule();
                $schedule->setDay($i);
                $schedule->setWorker($worker);
                $schedule->setStart(\DateTime::createFromFormat('H:i', $start));
                $schedule->setEnd(\DateTime::createFromFormat('H:i', $end));
                $entityManager->persist($schedule);
            }

            $entityManager->persist($worker);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success','L\'employé a bien été ajouté');

            return $this->redirectToRoute('saloon_manage_worker', array('saloon' => $saloon->getId()));
        }

        return $this->render('saloon/worker/new.html.twig', array(
        	'saloon' => $saloon,
        	'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/saloon/manage/worker/edit/{saloon}/{worker}", name="saloon_manage_worker_edit")
     */
    public function edit(Request $request, Saloon $saloon, User $worker)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(RegisterType::class, $worker, array('labelButton' => 'Modifier'));
        $form->remove('plainPassword');
        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isValid()) {

            $entityManager->persist($worker);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success','L\'employé a bien été modifié');

            return $this->redirectToRoute('saloon_manage_worker', array('saloon' => $saloon->getId()));
        }

        return $this->render('saloon/worker/edit.html.twig', array(
        	'saloon' => $saloon,
        	'form' => $form->createView(),
        ));
    }
}
