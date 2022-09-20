<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderSuccessController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/commande/merci/{stripeSessionId}', name: 'app_order_success')]
    public function index( Cart $cart,$stripeSessionId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        
        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
         }



         if (!$order->isIsPaid()) { 

            $cart->remove();


            // modifier le statut is paid de notre commande en base de données
            $order->setIspaid(1);

            // exécute
            $this->entityManager->flush();
         }
        
        
        
        
        // dd($order);

        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);
    }
}
