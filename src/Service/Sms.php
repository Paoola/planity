<?php

namespace App\Service;

class Sms {

    public function send($content, $to) {

        $login = "thoma39@msn.com";
        $password = "boocut_sms";
        $numbers = array($to);
        $recipients = array();
        foreach ($numbers as $n) {
          $recipients[] = array('value' => $n);
        }

        $postdata = array(
          'sms' => array(
            'authentication' => array(
            'username' => $login,
            'password' => $password
           ),
           'message' => array('text' => $content),
           'recipients' => array('gsm' => $recipients)
          )
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.smsfactor.com/send");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        $response = curl_exec($ch);
        curl_close($ch);

        return true;
    }
}
