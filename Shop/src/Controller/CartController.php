<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\CartService;
use MongoDB\Driver\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart')]
    public function index(CartService $cartService): Response
    {
        return $this->render('cart/index.html.twig', [
            "cart" => $cartService->getCart(),
            "total" => $cartService->getTotal()
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="cart_add")
     *
     */
    public function addProduct(Product $product, CartService $cartService){

        $cartService->addProduct($product);

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
}
