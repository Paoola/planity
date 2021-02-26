<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Price;
use App\Entity\User;
use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SlotsAvailableExtension extends AbstractExtension
{
    /**
     * @var ManageSlot
     */
    private $manageSlot;

    /**
     * @var bool
     */
    private $isAvailableSlots;

    public function __construct(
        ManageSlot $manageSlot
    )
    {
        $this->manageSlot = $manageSlot;
        $this->isAvailableSlots = false;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_available_slots', [$this, 'getAvailableSlots']),
        ];
    }

    public function getAvailableSlots(Price $price, User $worker, $time): array
    {
        $day = \DateTime::createFromFormat('U', $time);

        $slots = $this->manageSlot->freeTime($worker, $day, $price->getDuration() * 60);

        return $slots;

    }


}
