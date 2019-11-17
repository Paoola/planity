<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PauseRepository")
 */
class Pause
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $start;

    /**
     * @ORM\Column(type="datetime")
     */
    private $end;

    /**
     * @ORM\Column(type="boolean")
     */
    private $everyweek = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="pauses")
     */
    private $worker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Saloon", inversedBy="pauses")
     */
    private $saloon;

    public function __toString()
    {
        return $this->id . 'Salon : ' . $this->saloon->getId();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStart()
    {
        return $this->start;
    }

    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd()
    {
        return $this->end;
    }

    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    public function getEveryweek(): ?bool
    {
        return $this->everyweek;
    }

    public function setEveryweek(bool $everyweek): self
    {
        $this->everyweek = $everyweek;

        return $this;
    }

    public function getWorker(): ?User
    {
        return $this->worker;
    }

    public function setWorker(?User $worker): self
    {
        $this->worker = $worker;

        return $this;
    }

    public function getSaloon(): ?Saloon
    {
        return $this->saloon;
    }

    public function setSaloon(?Saloon $saloon): self
    {
        $this->saloon = $saloon;

        return $this;
    }
}
