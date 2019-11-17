<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ScheduleRepository")
 */
class Schedule
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $day;

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
    private $dayOff = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="schedules")
     */
    private $worker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Saloon", inversedBy="schedules")
     */
    private $saloon;

    public function __toString() {
        return $this->start->format('Y-m-d H:i:s'). ' -> ' . $this->end->format('Y-m-d H:i:s');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(int $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getDayOff(): ?bool
    {
        return $this->dayOff;
    }

    public function setDayOff(bool $dayOff): self
    {
        $this->dayOff = $dayOff;

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
