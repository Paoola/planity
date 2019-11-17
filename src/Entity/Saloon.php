<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SaloonRepository")
 * @Vich\Uploadable
 */
class Saloon
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $location;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $locationId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $latitude;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $longitude;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subscriptionStripeId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $facebookLink;

    /**
     * @ORM\Column(type="boolean")
     */
    private $bookingActive = 1;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPremium = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    private $onlinePayment = 0;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="saloons")
     * @ORM\JoinTable(name="saloon_managers")
     */
    private $managers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Schedule", mappedBy="saloon",cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $schedules;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Price", mappedBy="saloon", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $prices;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="saloonsWorker")
     * @ORM\JoinTable(name="saloon_workers")
     */
    private $workers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Pause", mappedBy="saloon")
     */
    private $pauses;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Slot", mappedBy="saloon")
     */
    private $slots;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stripeId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $promo;

    /**
     * @ORM\Column(type="integer")
     */
    private $money = 0;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="saloons", fileNameProperty="imageName")
     * 
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $imageName;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $customText;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Vacation", mappedBy="saloon", cascade={"persist", "remove"})
     */
    private $vacation;

    public function __construct()
    {
        $this->managers = new ArrayCollection();
        $this->schedules = new ArrayCollection();
        $this->prices = new ArrayCollection();
        $this->workers = new ArrayCollection();
        $this->pauses = new ArrayCollection();
        $this->slots = new ArrayCollection();
        $this->updatedAt = new \Datetime();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getLocationId(): ?string
    {
        return $this->locationId;
    }

    public function setLocationId(string $locationId): self
    {
        $this->locationId = $locationId;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSubscriptionStripeId(): ?string
    {
        return $this->subscriptionStripeId;
    }

    public function setSubscriptionStripeId(?string $subscriptionStripeId): self
    {
        $this->subscriptionStripeId = $subscriptionStripeId;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getFacebookLink(): ?string
    {
        return $this->facebookLink;
    }

    public function setFacebookLink(?string $facebookLink): self
    {
        $this->facebookLink = $facebookLink;

        return $this;
    }

    public function getBookingActive(): ?bool
    {
        return $this->bookingActive;
    }

    public function setBookingActive(bool $bookingActive): self
    {
        $this->bookingActive = $bookingActive;

        return $this;
    }

    public function getIsPremium(): ?bool
    {
        return $this->isPremium;
    }

    public function setIsPremium(bool $isPremium): self
    {
        $this->isPremium = $isPremium;

        return $this;
    }

    public function getOnlinePayment(): ?bool
    {
        return $this->onlinePayment;
    }

    public function setOnlinePayment(bool $onlinePayment): self
    {
        $this->onlinePayment = $onlinePayment;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getManagers(): Collection
    {
        return $this->managers;
    }

    public function addManager(User $manager): self
    {
        if (!$this->managers->contains($manager)) {
            $this->managers[] = $manager;
        }

        return $this;
    }

    public function removeManager(User $manager): self
    {
        if ($this->managers->contains($manager)) {
            $this->managers->removeElement($manager);
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
            $schedule->setSaloon($this);
        }

        return $this;
    }

    public function removeSchedule(Schedule $schedule): self
    {
        if ($this->schedules->contains($schedule)) {
            $this->schedules->removeElement($schedule);
            // set the owning side to null (unless already changed)
            if ($schedule->getSaloon() === $this) {
                $schedule->setSaloon(null);
            }
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
            $price->setSaloon($this);
        }

        return $this;
    }

    public function removePrice(Price $price): self
    {
        if ($this->prices->contains($price)) {
            $this->prices->removeElement($price);
            // set the owning side to null (unless already changed)
            if ($price->getSaloon() === $this) {
                $price->setSaloon(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getWorkers(): Collection
    {
        return $this->workers;
    }

    public function addWorker(User $worker): self
    {
        if (!$this->workers->contains($worker)) {
            $this->workers[] = $worker;
        }

        return $this;
    }

    public function removeWorker(User $worker): self
    {
        if ($this->workers->contains($worker)) {
            $this->workers->removeElement($worker);
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
            $pause->setSaloon($this);
        }

        return $this;
    }

    public function removePause(Pause $pause): self
    {
        if ($this->pauses->contains($pause)) {
            $this->pauses->removeElement($pause);
            // set the owning side to null (unless already changed)
            if ($pause->getSaloon() === $this) {
                $pause->setSaloon(null);
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
            $slot->setSaloon($this);
        }

        return $this;
    }

    public function removeSlot(Slot $slot): self
    {
        if ($this->slots->contains($slot)) {
            $this->slots->removeElement($slot);
            // set the owning side to null (unless already changed)
            if ($slot->getSaloon() === $this) {
                $slot->setSaloon(null);
            }
        }

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

    public function getPromo(): ?string
    {
        return $this->promo;
    }

    public function setPromo(?string $promo): self
    {
        $this->promo = $promo;

        return $this;
    }

    public function getMoney(): ?int
    {
        return $this->money;
    }

    public function setMoney(int $money): self
    {
        $this->money = $money;

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     */
    public function setImageFile(?File $image = null): void
    {
        $this->imageFile = $image;

        if (null !== $image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function getCustomText(): ?string
    {
        return $this->customText;
    }

    public function setCustomText(?string $customText): self
    {
        $this->customText = $customText;

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
        $newSaloon = $vacation === null ? null : $this;
        if ($newSaloon !== $vacation->getSaloon()) {
            $vacation->setSaloon($newSaloon);
        }

        return $this;
    }
}
