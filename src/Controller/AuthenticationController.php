<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

use App\Form\LoginType;
use App\Form\RegisterType;
use App\Form\ForgetPasswordType;
use App\Form\ForgetPasswordTokenType;
use App\Entity\User;
use App\Service\Mailer;

class AuthenticationController extends AbstractController
{
    /**
     * @Route("/connexion", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
	    $error = $authenticationUtils->getLastAuthenticationError();
	    $lastUsername = $authenticationUtils->getLastUsername();
        //nothing to do here if already logged in
        if ($this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('home');
        }

        try {
            if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                return $this->redirectToRoute('home');
            }
        } catch (AuthenticationCredentialsNotFoundException $e) {
            return $this->redirectToRoute('login');
        }
        $form = $this->createForm(LoginType::class);

	    return $this->render('authentication/login.html.twig', array(
	        'last_username' => $lastUsername,
	        'error'         => $error,
            'form'          => $form->createView(),
	    ));
    }

    /**
     * @Route("/inscription", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $entityManager->persist($user);
            $entityManager->flush();

            // Connexion automatique
            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);
            $this->get('session')->set('_security_main', serialize($token));

            return $this->redirectToRoute('home');
        }

        return $this->render('authentication/register.html.twig', array(
        	'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/mot-de-passe-oublie", name="forget_password")
     */
    public function forgetPassword(Request $request, Mailer $mailer)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(ForgetPasswordType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $entityManager->getRepository(User::class)->findOneByEmail($form->getData()['email']);
            if ($user !== null) {
                $token = uniqid();
                $user->setResetPassword($token);
                $entityManager->persist($user);
                $entityManager->flush();
                
                $mailer->send('Mot de passe oublié ?', $user->getEmail(), 'reset-password', array('token' => $token));

                return $this->render('authentication/reset-password-confirmation.html.twig');
            }
        }

        return $this->render('authentication/reset-password.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/mot-de-passe-oublie-token/{token}", name="forget_password_token")
     */
    public function forgetPasswordToken(Request $request, UserPasswordEncoderInterface $encoder, $token)
    {
        if ($token !== null) {
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneByResetPassword($token);
            if ($user !== null) {
                $form = $this->createForm(ForgetPasswordTokenType::class, $user);

                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $plainPassword = $form->getData()->getPlainPassword();
                    $encoded = $encoder->encodePassword($user, $plainPassword);
                    $user->setPassword($encoded);
                    $entityManager->persist($user);
                    $entityManager->flush();

                    return $this->redirectToRoute('login');
                }

                return $this->render('authentication/reset-password-token.html.twig', array(
                    'form' => $form->createView(),
                ));       
            }
        }
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        return true;
    }
}
