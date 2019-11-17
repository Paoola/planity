<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Mailgun\Mailgun;

class Mailer {

    private $mailer;
    private $templating;

    public function __construct(\Twig_Environment $templating, Container $container)
    {
        $this->fromMail = 'postmaster@mailgun.boocut.fr';
        $this->fromName = 'Boocut';
        $this->templating = $templating;
        $this->container = $container;
    }

    public function send($subject, $to, $template, $templateOptions)
    {
        $mgClient   = new Mailgun($this->container->getParameter('mailgun_api_key'));
        $domain     = $this->container->getParameter('mailgun_domain');
        $mailFrom   = $this->container->getParameter('mail_mail_from');
        $nameFrom   = $this->container->getParameter('mail_name_from');
        $mailTo     = $to;
        
        $result     = $mgClient->sendMessage($domain, array(
            'from' => "$nameFrom <$mailFrom>",
            'to' => "<$to>",
            'subject' => $subject,
            'html' => $this->templating->render('emails/'.$template.'.html.twig', $templateOptions)
        ));

        return true;
    }
}
