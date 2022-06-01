<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{

    private $session;
    private $repo;
    private $manager;

    public function __construct(SessionInterface $session, ProductRepository $repo, EntityManagerInterface $manager){
        $this->session = $session;
        $this->repo = $repo;
        $this->manager = $manager;
    }

    /**
     * @return array
     */
    public function getCart(CartService $cartService){

        if ($cartService->getSessionId()){
        $sessionId = $cartService->getSessionId();
        $this->session->setId($sessionId);

        }
        $cart = $this->session->get("cart", []);
        $productCart= [];

        foreach ($cart as $productId => $quantity){
            $item = [
                "product" => $this->repo->find($productId),
                "quantity" => $quantity
            ];

            $productCart[] = $item;
        }
        return $productCart;
    }

    /**
     * @param Product $product
     * @return void
     *
     */
    public function addProduct(Product $product): void{

        $cart = $this->session->get("cart", []);

        $productId = $product->getId();

        if (isset($cart[$productId])){
            $cart[$productId]++;
        } else {
            $cart[$productId] = 1;
        }

        $this->session->set("cart", $cart);
    }

    public function getTotal(){

        $total = 0;
        foreach($this->getCart() as $item) {
            $total += ($item['product']->getPrice() * $item['quantity']);
        }
        return $total;
    }

    /**
     * @param Product $product
     * @return void
     */
    public function removeProduct(Product $product): void{

        $cart = $this->session->get("cart");

        $productId = $product->getId();

        if (isset($cart[$productId])){
            $cart[$productId]--;
            if ($cart[$productId] == 0 ){

                unset($cart[$productId]);
            }
        }

        if ($cart == []){
            $this->session->remove();
        } else {

            $this->session->set("cart", $cart);
        }

    }

    /**
     *
     * @param Product $product
     * @return void
     */
    public function removeRow(Product $product): void{

        $cart = $this->session->get("cart");
        $productId = $product->getId();
        if (isset($cart[$productId])){
            unset($cart[$productId]);
        }

        if ($cart == []) {
            $this->emptyCart();
        } else {
        $this->session->set("cart", $cart);

        }

    }

    /**
     * @return void
     */
    public function emptyCart(){

        $this->session->get("cart");

        $this->manager->remove($this->isCartInDatabase());
        $this->manager->flush();

        $this->session->remove("cart");
    }


    /**
     * @return int|mixed
     */
    public function countProducts(){

        $count = 0;

        foreach($this->getCart() as $item){
            $count += $item["quantity"];
        }

        return $count;
    }


    /**
     * @param SessionInterface $session
     *
     */
    public function getSessionId(SessionInterface $session){
        return $this->session->getId();
    }


    public function isCartInDatabase(CartRepository $cartRepo){

        return $cartRepo->findOneBy("sessionId");
    }


    /*

    public function isInDatabase(){

        $session = new Session();

        $idSession = $session->getId();

        if ($this->getSessionId() == $idSession)
        {
            return true;
        }

        return false;
    }

    */
}