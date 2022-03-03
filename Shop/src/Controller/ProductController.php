<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/', name: 'product_index')]
    public function index(ProductRepository $repoProduct, SessionInterface $session): Response
    {

        $cart = $session->get("cart", []);

        $products = $repoProduct->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
            "cart" => $cart
        ]);
    }

}
