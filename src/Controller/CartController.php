<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Price;
use App\Form\OrderType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart")
     */
    public function index(Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        $order = new Order();

        $products = array();
        $total = 0;
        if ($session->get('cart')) {
            foreach ($session->get('cart') as $cart) {
                $product = $em->getRepository(Price::class)->find($cart);
                $products[] = $product;
                $total = $total + $product->getAmount();
                $order->addProduct($product);
            }
        }

        \Stripe\Stripe::setApiKey('sk_test_HtIzHZzux5WKTYvYPZy2KRz5');

        $intent = \Stripe\PaymentIntent::create([
            'amount' => intval($total) * 100,
            'currency' => 'eur',
            'payment_method_types' => ['card'],
        ]);

        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isValid()) {
            $em->persist($order);
            $em->flush();

            return $this->redirectToRoute('saloon_public_booking', array('price' => 4));
        }

        $saloon = $products && count($products) && $products[0] instanceof Price && $products[0]->getSaloon() ? $products[0]->getSaloon(): null;

        $price =  $products[0];

        return $this->render('cart/index.html.twig', array(
            'saloon' => $saloon,
            'products' => $products,
            'price' => $price,
            'form' => $form->createView(),
            'intent' => $intent
        ));
    }
}
