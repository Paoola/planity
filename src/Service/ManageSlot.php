<?php

namespace App\Service;

use DateTime;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Schedule;
use App\Entity\Pause;
use App\Entity\Slot;
use App\Entity\User as Worker;

class ManageSlot
{
    public function __construct(ObjectManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function freeTime(Worker $worker, $day, $duration)
    {
        $slots = array();

        $datetime = new \DateTime($day->format('Y-m-d H:i'), new \DateTimeZone('Europe/Paris'));
        $day_number = $day->format('w');

        //  A <- horaires du $worker                 pour $day
        //  B <- pauses du $worker                   pour $day
        //  C <- pauses_everyweek du $worker         pour $day
        //  D <- rendez-vous du $worker existants    pour $day
        $baseSchedules = $this->em->getRepository(Schedule::class)->findOneBy(
            array(
                'worker' => $worker,
                'day' => $day_number,
                'dayOff' => false
            )
        );

        $pauses = $this->em->getRepository(Pause::class)->findBy(
            array(
                'worker' => $worker,
                'everyweek' => false
            )
        );

        $pauses_week = $this->em->getRepository(Pause::class)->findBy(
            array(
                'worker' => $worker,
                'everyweek' => true
            )
        );

        $reserveds = $this->em->getRepository(Slot::class)->findBy(
            array(
                'worker' => $worker
            )
        );

        //|  atd <- A.debut converti en timestamp     debut de journée du worker
        //|  atf <- A.fin converti en timestamp       fin de journée du worker
        if (isset($baseSchedules)) {
            $atd = $baseSchedules->getStart();
            $atf = $baseSchedules->getEnd();

            $startOfDay = new Datetime(date($baseSchedules->getStart()->format('Y-m-d 00:00:00')));
            $interval = $startOfDay->diff($atd);/*
            $hours = $interval->format('%h');
            $minuts = $interval->format('%i');
            $atdTimestamp = ($hours * 60 * 60) + ($minuts * 60);

            $interval = $startOfDay->diff($atf);
            $hours = $interval->format('%h');
            $minuts = $interval->format('%i');
            $atfTimestamp = ($hours * 60 * 60) + ($minuts * 60);*/
        } else {
            $atd = 0;
            $atf = 0;
        }

        $date = new DateTime($day->format('Y-m-d'), new \DateTimeZone('Europe/Paris'));
        //$timestamp_day = $date->format('U'); // secondes depuis periode unix
        $timestamp_day = $date;
        $atd = $timestamp_day->setTime(12,00)->getTimestamp(); // debut
        $atf = $timestamp_day->setTime(18, 00)->getTimestamp(); // fin
//        dump(\DateTime::createFromFormat('U', $atd));
//        dump(\DateTime::createFromFormat('U', $atf));
//        die;
        $att = $atd;
        $dispo = true;
        $reprise = 0;
        $rdvf = 0;

        while ($att <= ($atf - $duration))
        {
            $rdvf = $att + $duration - 60;

            foreach($pauses as $pause)
            {
                $end =  $pause->getEnd()->format('U');
                $start =  $pause->getStart()->format('U');

                if ($rdvf <= $end && $rdvf >= $start){
                    $dispo = false;
                    if ($reprise < $end){
                        $reprise = $end;
                    }
                }
            }

            foreach($pauses_week as $pause_week)
            {
                $end =  $pause_week->getEnd()->format('U');
                $start =  $pause_week->getStart()->format('U');

                if ($rdvf <= $end && $rdvf >= $start){
                    $dispo = false;
                    if ($reprise < $end){
                        $reprise = $end;
                    }
                }
            }

            foreach($reserveds as $rdv)
            {
                $end =  $rdv->getEnd()->format('U');
                $start =  $rdv->getStart()->format('U');

                if ($rdvf <= $end && $rdvf >= $start){
                    $dispo = false;
                    if ($reprise < $end){
                        $reprise = $end;
                    }
                }
            }

            if ($dispo) {
                $res = new DateTime();

                $res->setTimestamp($att);
                $slots[] = $res;
                $att = $att + 60;
            } else {
                $att = $reprise;
            }

            $reprise = 0;
            $dispo = true;
        }

        return $slots;
    }
}
