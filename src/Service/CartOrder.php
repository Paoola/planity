<?php

declare(strict_types=1);

namespace App\Service;


use App\Repository\PriceRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartOrder implements ShoppingCart
{
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var PriceRepository
     */
    private $priceRepository;

    public function __construct(PriceRepository $priceRepository, SessionInterface $session)
    {
        $this->session = $session;
        $this->priceRepository = $priceRepository;
    }

    public function add(int $id)
    {
        $shoppingCart = $this->session->get('panier', []);

        if (!empty($shoppingCart[$id])){
            $shoppingCart[$id]++;
        }else{
            $shoppingCart[$id] = 1;
        }

        $this->session->set('shoppingCart', $shoppingCart);

    }

    public function remove(int $id)
    {
        
    }

    public function getFullCart(): array 
    {
        $panier = $this->session->get('panier', []);

        $panierWithData = [];

        foreach($panier as $id => $quantity){
            $panierWithData[] = [
                'product' => $this->priceRepository->find($id),
                'quantity' => $quantity
             ];
        }

        return $panierWithData;
        
    }

    public function computePrice() :float
    {
        $total = 0;

        foreach ($this->getFullCart() as $item){
            $total += $item['price']->getPrice() * $item['quantity'];
         }
        
    }

}
