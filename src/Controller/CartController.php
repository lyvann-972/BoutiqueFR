<?php

namespace App\Controller;


use App\Classe\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        
    }
    
    #[Route('/mon-panier', name: 'app_cart')]
    public function index(Cart $cart): Response
    {

       // dd($cartComplete);

        //  dd($cart->get());

        return $this->render('cart/index.html.twig', [
         
            'cartComplete' => $cart -> getFull()   //j'envoie ma clÃ© cart a ma variable cartComplete a ma vue cart/index

        ]);
    }

    #[Route('/cart/add/{id}', name: 'add_to_cart')]
    public function add(Cart $cart, $id):Response
    {
        $cart -> add($id);
        // dd($cart);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove', name: 'remove_my_cart')]
    public function remove(Cart $cart):Response
    {
        $cart -> remove();
        // dd($cart);
        return $this->redirectToRoute('app_products');
    }

     
    #[Route('/cart/delete{id}', name: 'delete_my_cart')]
    public function delete(Cart $cart, $id):Response
    {
        $cart -> delete($id);
        // dd($cart);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/decrease{id}', name: 'decrease_to_cart')]
    public function decrease(Cart $cart, $id):Response
    {
        $cart -> decrease($id);
        // dd($cart);
        return $this->redirectToRoute('app_cart');
    }
}