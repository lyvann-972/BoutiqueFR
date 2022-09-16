<?php 

namespace App\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Cart {

    private $requestStack;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager,  RequestStack $requestStack){
              $this -> requestStack = $requestStack;
              $this -> entityManager = $entityManager;

                        }

    //ajoute au panier
    public function add($id){
    $session = $this -> requestStack -> getSession();
    //on récupère les informations du panier a l'aide de la session
    $cart = $session -> get('cart', [

    ]);
    //si dans le panier il y a un produit déjà inséré 
    if (!empty($cart[$id])) {
        //on incrémente
        $cart[$id]++;
    }else {
        $cart[$id] = 1;
    }

    //on stock les informations du panier dans une session (cart)
    $session -> set('cart', $cart);

}
 //affiche le panier 
public function get(){

        $session = $this -> requestStack->getSession();
        return $session->get('cart');
}

public function remove(){

        $session = $this -> requestStack->getSession();
        return $session->remove('cart');
}

public function delete($id){

    //je lance la session 
    $session = $this -> requestStack->getSession();
    
    //je récupère les informations de ma session cart
    $cart = $session->get('cart', []);

    //je suprime l'élément (produit ) correspondant du panier
    unset($cart[$id]);

    // on redéfinit la nouvelle valeur dans la session cart
    return $session->set('cart', $cart);
}



public function decrease($id){

    $session = $this->requestStack->getSession();
    $cart = $session->get('cart', []);

    if ($cart[$id] > 1) {
      
      $cart[$id]--;  // retirer une quantité

    }else {

      unset($cart[$id]);   //supprimer mon produit 
      
    }
      //vérifier si la quantité de notre produit 

      return $session->set('cart',$cart); 
    
   }



   public function getFull()
  {
    $cartComplete = [];

    if($this->get()) {
      foreach ($this->get() as $id => $quantity) {
        // Je récupère l'ID du produit en base de données
        $product_object = $this->entityManager->getRepository(Product::class)->findOneById($id);

        // SI le produit n'existe pas
        if (!$product_object) {
          // On le supprime du panier
          $this->delete($id);
          continue; //break
        }

        $cartComplete[] = [
          'product' => $product_object,
          'quantity' => $quantity
        ];
      } 
    }

    return $cartComplete;

  }
}