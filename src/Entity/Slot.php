<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SlotRepository")
 */
class Slot
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
    private $isPaid = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isValid = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $confirmationCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stripeId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Price", inversedBy="slots")
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="slots")
     */
    private $worker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Saloon", inversedBy="slots")
     */
    private $saloon;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer", inversedBy="slots", cascade={"persist"})
     */
    private $customer;

    public function __toString()
    {
        return $this->id . 'Client : ' . $this->customer->getId();
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

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getIsPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): self
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    public function getIsValid(): ?bool
    {
        return $this->isValid;
    }

    public function setIsValid(bool $isValid): self
    {
        $this->isValid = $isValid;

        return $this;
    }

    public function getConfirmationCode(): ?string
    {
        return $this->confirmationCode;
    }

    public function setConfirmationCode(string $confirmationCode): self
    {
        $this->confirmationCode = $confirmationCode;

        return $this;
    }

    public function getStripeId(): ?string
    {
        return $this->stripeId;
    }

    public function setStripeId(?string $stripeId): self
    {
        $this->stripeId = $stripeId;

        return $this;
    }

    public function getPrice(): ?Price
    {
        return $this->price;
    }

    public function setPrice(?Price $price): self
    {
        $this->price = $price;

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

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }
}
