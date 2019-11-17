<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VacationRepository")
 */
class Vacation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Saloon", inversedBy="vacation", cascade={"persist", "remove"})
     */
    private $saloon;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="vacation", cascade={"persist", "remove"})
     */
    private $worker;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $days = [];

    public function getId(): ?int
    {
        return $this->id;
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

    public function getWorker(): ?User
    {
        return $this->worker;
    }

    public function setWorker(?User $worker): self
    {
        $this->worker = $worker;

        return $this;
    }

    public function getDays(): ?array
    {
        return $this->days;
    }

    public function setDays(?array $days): self
    {
        $this->days = $days;

        return $this;
    }
}
