<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Price;
use App\Repository\PriceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/panier/add/{price}", name="cart_add")
     */
    public function add(Request $request, $price, SessionInterface $session, PriceRepository $priceRepository)
    {
        $methodResponse = $request->query->get('response');
        $shoppingCart = $session->get('shoppingCart', []);
        $nb = 0;

        if ($price) {
            if (!empty($shoppingCart[$price])) {
                $shoppingCart[$price]++;
            } else {
                //limit number item to add to cart
                $shoppingCart[(int)$price] = 1;
            }
            $session->set('shoppingCart', $shoppingCart);
        }

        if ($methodResponse !== 'JSON') {
            if ($request->get('response') !== 'HTML') {
                return $this->redirectToRoute("cart_index");
            }

            return $this->index($priceRepository, $session);

        }


        foreach($shoppingCart as $id => $quantity){
            $nb += $quantity;
        }

        return new Response($nb);
    }

    /**
     * @Route("/panier", name="cart_index")
     */
    public function index(PriceRepository $priceRepository, SessionInterface $session)
    {

        $panier = $session->get('shoppingCart', []);

        $panierWithData = [];

        foreach($panier as $id => $quantity){
            $panierWithData[] = [
                'product' => $priceRepository->find($id),
                'quantity' => $quantity
            ];
        }

        $total = 0;

        foreach ($panierWithData as $item){

            $totalItem = $item['product']->getAmount() * $item['quantity'];

            $total += $totalItem;
        }



        return $this->render('cart/index.html.twig',
            [
                'items' => $panierWithData,
                'total' => $total,
            ]
        );
    }
}
