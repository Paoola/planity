<?php

declare(strict_types=1);

namespace App\Service;

interface ShoppingCart
{
    public function add(int $id);

    public function remove(int $id);

    public function getFullCart(): array;

    public function computePrice() :float;
}
