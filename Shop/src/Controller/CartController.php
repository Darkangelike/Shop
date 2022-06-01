<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Cart;
use App\Entity\Product;
use App\Form\AddressType;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use MongoDB\Driver\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart')]
    public function index(CartService $cartService, EntityManagerInterface $manager, Request $request): Response
    {
        $address = new Address();

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $address->setUser($this->getUser());
            $manager->persist($address);
            $manager->flush();
        }

        return $this->renderForm('cart/index.html.twig', [
            "cart" => $cartService->getCart(),
            "total" => $cartService->getTotal(),
            "form" => $form
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="cart_add")
     *
     */
    public function addProduct(Product $product, CartService $cartService, SessionInterface $session,
                               EntityManagerInterface $manager){

        if ($product){

        $cartService->addProduct($product);

        $cart = $cartService->isCartInDatabase();

        if (!$cart){
        $cart = new Cart();
        if ($this->getUser()){
            $cart->setUser($this->getUser());
        }

        }
        $cart->setSe($cartService->getSessionId());


        $cart->setCreatedAt(new \DateTime());
        $cart->setTotal($cartService->getTotal());

        $session["cart"] = $cart;

        $manager->persist($cart);
        $manager->flush();

        }

        return $this->redirectToRoute("cart");
    }


    /**
     *
     * @Route("/cart/remove/{id}", name="cart_remove")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeProduct(CartService $cartService, Product $product)
    {

        $cartService->removeProduct($product);

        return $this->redirectToRoute("cart");
    }


    /**
     * @Route("/cart/removeRow/{id}", name="cart_removeRow")
     * @param SessionInterface $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     */
    public function removeRow(CartService $cartService, Product $product){

        $cartService->removeRow($product);

        return $this->redirectToRoute("cart");
    }

    /**
     *
     * @Route("/cart/removeAll", name="cart_removeAll")
     * @param CartService $cartService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAll(CartService $cartService){

        $cartService->emptyCart();

        return $this->redirectToRoute("cart");
    }
}
