<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountAddressController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/compte/adresses', name: 'account_address')]
    public function index(): Response
    {
        // dd($this->getUser());
        return $this->render('account/address.html.twig', [
            
        ]);
    }
    
    // je passe le paramètre de ma root 
    #[Route('/compte/ajouter-une-adresse', name: 'account_address_add')]
    public function add(Cart $cart, Request $request): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $address->setUser($this->getUser());
            // dd($address);

            // Fige la data
            $this->entityManager->persist($address);

            // Exécute
            $this->entityManager->Flush();

            if ($cart->get()) {
                // JE redirige vers commande
                return $this->redirectToRoute('order');
            } else {
                return $this->redirectToRoute('account_address');
            }

           return $this->redirectToRoute('account_address');

        }
        return $this->render('account/address_form.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    // je passe le paramètre de ma root 
    #[Route('/compte/modifier-une-adresse/{id}', name: 'account_address_edit')]
    public function edit(Request $request, $id): Response
    {
        // je recup l'adresse concerné à l'aide de doctrine en base de donnés (id)
        $address = $this->entityManager->getRepository(Address::class)->findOneById($id);

        // Si il n'y a aucune adresse ou que l'utilisateur ne correspond pas à celui actuellement connecté
        if (!$address || $address->getUser() != $this->getUser()) {
            return $this->redirectToRoute('account_address');
        }

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $address->setUser($this->getUser());
            // dd($address);

            // Exécute
            $this->entityManager->Flush();
           return $this->redirectToRoute('account_address');

        }
        return $this->render('account/address_form.html.twig', [
            'form'=>$form->createView()
        ]);
    }

    // je passe le paramètre de ma root 
    #[Route('/compte/supprimer-une-adresse/{id}', name: 'account_address_delete')]
    public function delete($id): Response
    {
        // je recup l'adresse concerné à l'aide de doctrine en base de donnés (id)
        $address = $this->entityManager->getRepository(Address::class)->findOneById($id);

        // Si il n'y a aucune adresse ou que l'utilisateur ne correspond pas à celui actuellement connecté
        if (!$address || $address->getUser() == $this->getUser()) {
            $this->entityManager->remove($address);
        }

            // dd($address);

            // Exécute
            $this->entityManager->Flush();
           return $this->redirectToRoute('account_address');

        }
    }

