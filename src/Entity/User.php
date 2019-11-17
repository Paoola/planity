<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="Email déjà utilisé")
 */
class User implements UserInterface
{
    const ROLE_DEFAULT = 'ROLE_USER';
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="array")
     */
    private $roles = [];

    private $plainPassword;

    /**
     *
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Saloon", mappedBy="managers")
     */
    private $saloons;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Schedule", mappedBy="worker", orphanRemoval=true)
     */
    private $schedules;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Price", mappedBy="workers")
     */
    private $prices;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Saloon", mappedBy="workers")
     */
    private $saloonsWorker;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Pause", mappedBy="worker")
     */
    private $pauses;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Slot", mappedBy="worker")
     */
    private $slots;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Vacation", mappedBy="worker", cascade={"persist", "remove"})
     */
    private $vacation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resetPassword;

    public function __toString() {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function __construct()
    {
        $this->roles = array('ROLE_USER');
        $this->createdAt = new \Datetime('now');
        $this->updatedAt = new \Datetime('now');
        $this->saloons = new ArrayCollection();
        $this->schedules = new ArrayCollection();
        $this->prices = new ArrayCollection();
        $this->saloonsWorker = new ArrayCollection();
        $this->pauses = new ArrayCollection();
        $this->slots = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
    }

    public function getUsername()
    {
        return $this->email;
    }

    /**
     * @return Collection|Saloon[]
     */
    public function getSaloons(): Collection
    {
        return $this->saloons;
    }

    public function addSaloon(Saloon $saloon): self
    {
        if (!$this->saloons->contains($saloon)) {
            $this->saloons[] = $saloon;
            $saloon->addManager($this);
        }

        return $this;
    }

    public function removeSaloon(Saloon $saloon): self
    {
        if ($this->saloons->contains($saloon)) {
            $this->saloons->removeElement($saloon);
            $saloon->removeManager($this);
        }

        return $this;
    }

    /**
     * @return Collection|Schedule[]
     */
    public function getSchedules(): Collection
    {
        return $this->schedules;
    }

    public function addSchedule(Schedule $schedule): self
    {
        if (!$this->schedules->contains($schedule)) {
            $this->schedules[] = $schedule;
            $schedule->setWorker($this);
        }

        return $this;
    }

    public function removeSchedule(Schedule $schedule): self
    {
        if ($this->schedules->contains($schedule)) {
            $this->schedules->removeElement($schedule);
            // set the owning side to null (unless already changed)
            if ($schedule->getWorker() === $this) {
                $schedule->setWorker(null);
            }
        }

        return $this;
    }

    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @return Collection|Price[]
     */
    public function getPrices(): Collection
    {
        return $this->prices;
    }

    public function addPrice(Price $price): self
    {
        if (!$this->prices->contains($price)) {
            $this->prices[] = $price;
            $price->addWorker($this);
        }

        return $this;
    }

    public function removePrice(Price $price): self
    {
        if ($this->prices->contains($price)) {
            $this->prices->removeElement($price);
            $price->removeWorker($this);
        }

        return $this;
    }

    /**
     * @return Collection|Saloon[]
     */
    public function getSaloonsWorker(): Collection
    {
        return $this->saloonsWorker;
    }

    public function addSaloonsWorker(Saloon $saloonsWorker): self
    {
        if (!$this->saloonsWorker->contains($saloonsWorker)) {
            $this->saloonsWorker[] = $saloonsWorker;
            $saloonsWorker->addWorker($this);
        }

        return $this;
    }

    public function removeSaloonsWorker(Saloon $saloonsWorker): self
    {
        if ($this->saloonsWorker->contains($saloonsWorker)) {
            $this->saloonsWorker->removeElement($saloonsWorker);
            $saloonsWorker->removeWorker($this);
        }

        return $this;
    }

    /**
     * @return Collection|Pause[]
     */
    public function getPauses(): Collection
    {
        return $this->pauses;
    }

    public function addPause(Pause $pause): self
    {
        if (!$this->pauses->contains($pause)) {
            $this->pauses[] = $pause;
            $pause->setWorker($this);
        }

        return $this;
    }

    public function removePause(Pause $pause): self
    {
        if ($this->pauses->contains($pause)) {
            $this->pauses->removeElement($pause);
            // set the owning side to null (unless already changed)
            if ($pause->getWorker() === $this) {
                $pause->setWorker(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Slot[]
     */
    public function getSlots(): Collection
    {
        return $this->slots;
    }

    public function addSlot(Slot $slot): self
    {
        if (!$this->slots->contains($slot)) {
            $this->slots[] = $slot;
            $slot->setWorker($this);
        }

        return $this;
    }

    public function removeSlot(Slot $slot): self
    {
        if ($this->slots->contains($slot)) {
            $this->slots->removeElement($slot);
            // set the owning side to null (unless already changed)
            if ($slot->getWorker() === $this) {
                $slot->setWorker(null);
            }
        }

        return $this;
    }

    public function getVacation(): ?Vacation
    {
        return $this->vacation;
    }

    public function setVacation(?Vacation $vacation): self
    {
        $this->vacation = $vacation;

        // set (or unset) the owning side of the relation if necessary
        $newWorker = $vacation === null ? null : $this;
        if ($newWorker !== $vacation->getWorker()) {
            $vacation->setWorker($newWorker);
        }

        return $this;
    }

    public function getResetPassword(): ?string
    {
        return $this->resetPassword;
    }

    public function setResetPassword(string $resetPassword): self
    {
        $this->resetPassword = $resetPassword;

        return $this;
    }
}
