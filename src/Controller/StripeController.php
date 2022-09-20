<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Classe\Cart;
use App\Entity\Order;
use Stripe\Checkout\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{

    #[Route('/commmande/create-session/{reference}', name: 'stripe_create_session')]
    public function index(EntityManagerInterface $entityManagerInterface, $reference, Cart $cart): Response
    {
        
        $products_for_stripe = [];

        //route 
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        $order = $entityManagerInterface->getRepository(Order::class)->findOneByReference($reference);

        

        if (!$order) {
            new JsonResponse(['error' => 'order']);
        }

        foreach ($cart->getFull() as $product) {
            //intÃ©gration de STRIPE
            $products_for_stripe[]= [ //permet d'afficher le recapitulatif de la commande avant payment
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product['product']->getPrice(),
                    'product_data' => [
                        'name' => $product['product']->getName(),
                        'images' => ["http://127.0.0.1:8000/public/uploads"],
                    ],
                 ],
                'quantity' => ($product['quantity'])

            ];

        }
        
        // dd($order->getOrderDetails()->getValues());

        //transporteur
        $carrier_for_stripe[]= [ //permet d'afficher le transporteur avant payment
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => ["http://127.0.0.1:8000/public/uploads"],
                ],
             ],
            'quantity' => 1

        ];
           
             // This is your test secret API key.
            Stripe::setApiKey('sk_test_51LieBcK9e2s2wv9Gi1NS8b76qXmJ6xCJk5CSUfVvlCqK9IdYn4CFmeRxgt8lOmKbFWUK3vtarSS76Zhs8APlKni400ZjmaraJV');
            //dd($order);
            $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),   
            'payment_method_types' => ['card'],
            'line_items' => [

            $products_for_stripe 

            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
          ]);
                $order->setStripeSessionId($checkout_session->id);

                $entityManagerInterface->flush();
          //$response = new JsonResponse(['id' => $checkout_session->id]);
          return $this->redirect($checkout_session->url);
        
       
    }

} 
